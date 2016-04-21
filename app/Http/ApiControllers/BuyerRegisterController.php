<?php

namespace App\Http\ApiControllers;

use Illuminate\Http\Request;
use App\Http\ApiControllers\Traits\UniqueTrait;
use App\Utils\Json\ResponseTrait;
use App\Repositories\User\UserRepositoryInterface;
use App\Utils\AuthUser\SessionUser;
use Illuminate\Support\Facades\DB;
use App\Helper\Mail;

class BuyerRegisterController extends Controller
{
    use UniqueTrait;
    use ResponseTrait;

    private $errCode = 451;
    private $authUser;
    private $user;


    function __construct(UserRepositoryInterface $user)
    {
        $this->user = $user;
        $this->authUser = new SessionUser();
    }

    public function checkMobileIsAvailable(Request $request)
    {
        $userMobile = $request->input('mobile');
        if ($this->exist('users', 'mobile', $userMobile)) {
            $ret = $this->requestFailed($this->errCode, "当前手机号已被使用");

        } else {
            $ret = $this->requestSucceed();
        }
        return $this->response->array($ret);
    }

    public function updateUserInfoAndCreateBuyerRecord(Request $request)
    {
        $userMobile = $request->input('mobile');
        $userEmail = $request->input('email');

        // 检查邮箱是否可用
        if ($this->exist('users', 'email', $userEmail)) {
            $ret = $this->requestFailed($this->errCode, "当前邮箱已被使用");
            return $this->response->array($ret);
        }
        DB::beginTransaction();
        $updated = $this->user->updateInfo(
            $this->authUser->currentUser(),
            array(
                'mobile' => $userMobile,
                'email' => $userEmail
            )
        );
        $created = $this->user->createBuyerAccount($this->authUser->currentUser());

        if($updated && $created) {
            DB::commit();
            $ret = $this->requestSucceed();
            Mail::MailToBuyerForRegistrySuccessfully(array($userEmail));
        } else {
            DB::rollBack();
            $ret = $this->requestFailed(400, "系统存储失败请重试");
        }
        return $this->response->array($ret);
    }

}