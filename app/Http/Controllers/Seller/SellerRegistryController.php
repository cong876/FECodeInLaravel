<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Helper\SellerUseOnce;
use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Seller\SellerRepositoryInterface;
use App\Helper\Ucpaas;
use App\Helper\Mail;

class SellerRegistryController extends Controller
{
    function __construct(UserRepositoryInterface $user, SellerRepositoryInterface $seller)
    {
        // 买手注册需要微信认证登录，如果是新用户存到表中;
        // 因为买手第一次注册时，没有seller表中的记录所以分离，该处仅使用微信中间件
        $this->middleware('wechatauth');

        $this->user = $user;
        $this->seller = $seller;
    }

    /*
     * 买手注册页
     */
    public function register(Request $request)
    {
        $user = Auth::user(); // 登录用户
        $openid = $user->openid;

        $suo = new SellerUseOnce();
        $code = $request->get('regCode');
        $record = Cache::get($code);
        // 第一次使用绑定
        if ($record === '') {
            // 运营打开链接不作废,防止误操作

            if($user->employee) {
                return view('register');
            }
            else {
                $suo->bindRegisterCodeToOpenId($code, $openid);
                return view('register');
            }
        }
        // 匹配
        if ($record === $openid && $openid != null && $record !== null) {
            return view('register');
        } // 邀请链接不匹配
        else {
            abort(441);
        }
        return view('register');

    }

    /*
     * 添加买手
     */
    public function createSeller(Request $request)
    {
        $user = Auth::user();
        $update_email = $request->email;
        $realName = $request->realName;
        if($update_email == "")
        {
            $this->user->updateInfo($user, array('secure_password' => bcrypt($request->password)));
        }
        else{
        if (count(User::where('email', $update_email)->get()) != 0) {
            return response()->json(0);
        } else {
            $this->user->updateInfo($user, array('email' => $request->email,
                'mobile' => $request->mobile,
                'secure_password' => bcrypt($request->password)));}}
            if (count($user->seller) == 0) {
                $this->seller->createSellerInfo(array('country_id' => $request->country,
                    'real_name' => $realName, 'name_pinyin' => '',
                    'name_abbreviation' => ''),
                    $user->hlj_id);

                // 登录当前买手信息到Session
                Session::put('sellerRole', 1);
                // 使邀请链接无效
                $suo = new SellerUseOnce();
                $suo->killTheUsedCode($user->openid);

            }
            if($update_email != "")
            {Mail::MailToSellerForRegistrySuccessfully(array($update_email));}
            else{Mail::MailToSellerForRegistrySuccessfully(array($user->email));}
            return response()->json(1);

    }

    /*
 *
 * 认证手机
 *
 */
    public function authMobile(Request $request)
    {
        if (count(User::where('mobile', $request->mobile)->get()) == 0) {
            return response()->json(1);
        } else {
            return response()->json(0);
        }
    }

    /*
     *
     * 更新手机号
     *
     */
    public function updateMobile(Request $request)
    {
        $update_mobile = $request->mobile;
        $hlj_id = Auth::user()->hlj_id;
        $user = User::where('hlj_id', $hlj_id)->first();
        if (count(User::where('mobile', $update_mobile)->get()) != 0) {
            return response()->json(0);
        } else {
            $user->mobile = $update_mobile;
            $user->save();
            return response()->json(1);
        }

    }

    /*
     *
     * 更新email
     *
     */
    public function updateEmail(Request $request)
    {
        $update_email = $request->email;
        $hlj_id = Auth::user()->hlj_id;
        $user = User::where('hlj_id', $hlj_id)->first();
        if (count(User::where('mobile', $update_email)->get()) != 0) {
            return response()->json(0);
        } else {
            $user->email = $update_email;
            $user->save();
            return response()->json(1);
        }

    }

    // 获得验证码
    public function getVerifySMS(Request $request)
    {
        $zone = $request->zone;
        $mobile = $request->mobile;
        if (($zone + 0) == 86) {
            // 国内使用LeanCloud发送
            $data = [
                "mobilePhoneNumber" => $mobile,
            ];
            $url = "https://api.leancloud.cn/1.1/requestSmsCode";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-AVOSCloud-Application-Id: 1uCLCKq2T7Y4jh0VxXgBOLpV',
                'X-AVOSCloud-Application-Key: Sh2vHA09uWO21vuvBCTL8bop', 'Content-Type: application/json'));
            $ret = curl_exec($ch);
            $phpObject = json_decode($ret);
            if (empty($phpObject->error)) {
                return response()->json(['status' => true]);
            } else {
                return response()->json(['status' => false]);
            }
        } else {
            // 生成10分钟有效的验证码
            $verifyCode = rand(pow(10, (6 - 1)), pow(10, 6) - 1);
            $expiresAt = \Carbon\Carbon::now()->addMinutes(10);
            Cache::put($mobile . '_verify', $verifyCode, $expiresAt);
            // 国外短信
            $options['accountsid'] = 'abd00c1ce95b4f49c5101c3a02eac405';
            $options['token'] = '016a57b4137f445e1bbd430afaa4a7a0';
            $ucpass = new Ucpaas($options);

            //数据区域
            $appId = "8c8e46c57a554d2b84e1756d9611bfd0";
            $to = '00' . $zone . $mobile;
            $templateId = "12844";
            $param = $verifyCode.",10";
//            $out = $ucpass->voiceCode($appId, $verifyCode, $to, 'json');
            $out = $ucpass->templateSMS($appId, $to, $templateId, $param, 'json');
            $phpObj = json_decode($out);
            if ($phpObj->resp->respCode === '000000') {
                return response()->json(['status' => true]);
            } else {
                return response()->json(['status' => false]);
            }
        }
    }

    public function verifyCode(Request $request)
    {
        $zone = $request->zone;
        $mobile = $request->mobile;
        $code = $request->code;
        if (($zone + 0) == 86) {
            $url = "https://api.leancloud.cn/1.1/verifySmsCode/" . $code . "?mobilePhoneNumber=" . $mobile;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-AVOSCloud-Application-Id: 1uCLCKq2T7Y4jh0VxXgBOLpV',
                'X-AVOSCloud-Application-Key: Sh2vHA09uWO21vuvBCTL8bop', 'Content-Type: application/json'));
            $ret = curl_exec($ch);
            $phpObject = json_decode($ret);
            if (empty($phpObject->error)) {
                return response()->json(['status' => true]);
            } else {
                return response()->json(['status' => false]);
            }
        } else {
            $serverCode = Cache::get($mobile . '_verify');
            if (!empty($serverCode) && ($serverCode == $code)) {
                return response()->json(['status' => true]);
            } else {
                return response()->json(['status' => false]);
            }
        }
    }
}
