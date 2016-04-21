<?php

namespace App\Http\ApiControllers\User;

use App\Http\ApiControllers\Controller;
use App\Models\User;
use Cache;
use Illuminate\Http\Request;
use App\Utils\Json\ResponseTrait;

class UserController extends Controller
{
    use ResponseTrait;

    public function updateMobileOrEmail(Request $request, $user_id)
    {
        $user = User::find($user_id);
        if (!$user) {
            $ret = $this->requestFailed(400, "当前用户不存在");
            return $this->response->array($ret);
        }

        $mobile = $request->mobile ? $request->mobile : null;
        $email = $request->email ? $request->email : null;

        if ($mobile && $email) {
            $ret = $this->requestFailed(400, "不能同时更新电话和邮箱");
            return $this->response->array($ret);
        }

        if (!$mobile && !$email) {
            $ret = $this->requestFailed(400, "无更新数据,更新失败");
            return $this->response->array($ret);
        }

        if ($email) {
            $existedUser = User::where('email', $email)->first();
            if ($existedUser && $existedUser->hlj_id != $user_id) {
                $ret = $this->requestFailed(400, "当前邮箱已被注册");
            } else if ($existedUser && $existedUser->hlj_id == $user_id) {
                $ret = $this->requestSucceed();
            } else {
                $user->email = $email;
                $saved = $user->save();
                if ($saved) {
                    $ret = $this->requestSucceed();
                } else {
                    $ret = $this->requestFailed(400, "修改信息失败,请重试");
                }
            }
            return $this->response->array($ret);
        }

        if ($mobile) {
            $existedUser = User::where('mobile', $mobile)->first();
            if ($existedUser && $existedUser->hlj_id != $user_id) {
                $ret = $this->requestFailed(400, "当前手机号已被注册");
            } else if ($existedUser && $existedUser->hlj_id == $user_id) {
                $ret = $this->requestSucceed();
            } else {
                $user->mobile = $mobile;
                $saved = $user->save();
                if ($saved) {
                    $ret = $this->requestSucceed();
                } else {
                    $ret = $this->requestFailed(400, "修改信息失败,请重试");
                }
            }
            return $this->response->array($ret);
        }
    }

    public function checkKill($user_id, $type)
    {

        if (!Cache::get('SecKill:Users')) {
           Cache::forever('SecKill:Users', []);
        }
        if (!Cache::get('SecKill:CanClick')) {
            Cache::forever('SecKill:CanClick', []);
        }
        if (!Cache::get('SecKill:CannotClick')) {
            Cache::forever('SecKill:CannotClick', []);
        }
        if (!Cache::get('SecKill:Remind')) {
            Cache::forever('SecKill:Remind', []);
        }
        $arr = Cache::get('SecKill:Users');
        $can = Cache::get('SecKill:CanClick');
        $cannot = Cache::get('SecKill:CannotClick');
        $remind = Cache::get('SecKill:Remind');

        if ($type == 'buy') {
            if (!in_array($user_id, $arr)) {
                array_push($arr, $user_id);
                Cache::forever('SecKill:Users', $arr);

                if (array_key_exists($user_id, $can)) {
                    $can[$user_id] += 1;
                } else {
                    $can[$user_id] =  1;
                }
                Cache::forever('SecKill:CanClick', $can);
                $ret = $this->requestFailed(400, "商品已被其他人抢光了");

            } else {
                if (array_key_exists($user_id, $cannot)) {
                    $cannot[$user_id] += 1;
                } else {
                    $cannot[$user_id] =  1;
                }
                Cache::forever('SecKill:CannotClick', $cannot);
                $ret = $this->requestSucceed();
            }

        }

        if ($type == 'remind') {
            if (!in_array($user_id, $remind)) {
                array_push($remind, $user_id);
                Cache::forever('SecKill:Remind', $remind);
                $ret = $this->requestFailed(400, "设置提醒成功");
            } else {
                $ret = $this->requestSucceed();
            }
        }

        return $this->response->array($ret);
    }
}