<?php

namespace App\Http\Middleware;

use Closure;
use Overtrue\Wechat\Auth as WXAuth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class WechatAuth
{
    public function handle($request, Closure $next)
    {
        // 调用的API信息
        $appId = config('wx.appId');
        $secret = config('wx.appSecret');
        // 判断Session是否有登陆用户信息
        if (empty(Auth::check())) {
            // 如果用户未登录，使用微信snsapi_userinfo作为登录依据，第一次用户需要统一授权。
            $auth = new WXAuth($appId, $secret);
            $authUser = $auth->authorize($to = null, $scope = 'snsapi_userinfo', $state = 'STATE');
            // 使用openid判断用户身份
            $user = User::where('openid', $authUser->openid)->first();

            // 已经存储过用户信息，但是Session丢失
            if ($user) {
                // 关注过公众号，但未授权，未使用过功能
                if (empty($user->unionid)) {
                    $this->updateUserTable($user, $authUser);
                }
                // 登录当前用户
                Auth::login($user);

                // 判断是否更新头像
                $lastUpdated = $this->getUpdateTime($user);
                if ($this->isUserInformationExpired($lastUpdated)) {
                    $updated = $this->updateUserTable($user, $authUser);
                    if ($updated) {
                        $this->updateTimeString($user);
                    }
                }

                // 判断当前用户是否是买手，是则附加上买手Session信息
                $seller = $user->seller;
                if (!empty($seller)) {
                    if ($seller->seller_type == 1 || $seller->seller_type == 3 || $seller->seller_type == 4) {
                        Session::put('sellerRole', $seller->seller_type);
                    }
                }
                return $next($request);
            } else {
                // 全新用户，并且没有关注过公众号，三目操作符防止微信用户某些信息不填写
                $data = [];
                $data['openid'] = $authUser['openid'];
                $data['nickname'] = isset($authUser['nickname']) ? $authUser['nickname'] : '';
                $data['sex'] = isset($authUser['sex']) ? $authUser['sex'] : '';
                $data['province'] = isset($authUser['province']) ? $authUser['province'] : '';
                $data['city'] = isset($authUser['city']) ? $authUser['city'] : '';
                $data['country'] = isset($authUser['country']) ? $authUser['country'] : '';
                $data['headimgurl'] = isset($authUser['headimgurl']) ? $authUser['headimgurl'] : '';
                $data['privilege'] = isset($authUser['privilege']) ? json_encode($authUser['privilege']) : '';
                $data['unionid'] = isset($authUser['unionid']) ? $authUser['unionid'] : '';
                $newUser = User::create($data);
                // 是否存储成功
                if (!$newUser) {
                    return false;
                }
                Auth::login($newUser);
                return $next($request);
            }
        } else {
            $user = Auth::user();
            // 用户存在, 如果没有微信资料则拉取
            if (empty($user->unionid)) {
                $auth = new WXAuth($appId, $secret);
                $authUser = $auth->authorize($to = null, $scope = 'snsapi_userinfo', $state = 'STATE');
                $this->updateUserTable($user, $authUser);
            }

            // 判断是否更新头像
            $lastUpdated = $this->getUpdateTime($user);
            if ($this->isUserInformationExpired($lastUpdated)) {
                $auth = new WXAuth($appId, $secret);
                $authUser = $auth->authorize($to = null, $scope = 'snsapi_userinfo', $state = 'STATE');
                $updated = $this->updateUserTable($user, $authUser);
                if ($updated) {
                    $this->updateTimeString($user);
                }
            }

            $seller = $user->seller;
            if (!empty($seller)) {
                if ($seller->seller_type == 1 || $seller->seller_type == 3 || $seller->seller_type == 4) {
                    Session::put('sellerRole', $seller->seller_type);
                }
            }
            return $next($request);
        }
    }

    public function getUpdateTime($user)
    {
        $key = 'User:' . $user->hlj_id . ":LastUpdated";
        return Cache::get($key, function () use ($key) {
            $updateTimeString = strtotime(date('Y-m-d H:i:s'));
            Cache::forever($key, $updateTimeString);
            return $updateTimeString;
        });
    }

    public function isUserInformationExpired($lastUpdatedTimeString)
    {
        $time_expire = $lastUpdatedTimeString + 15 * 24 * 60 * 60;
        if (strtotime(date('Y-m-d H:i:s')) > $time_expire) {
            return true;
        } else {
            return false;
        }
    }

    public function updateTimeString($user)
    {
        $key = 'User:' . $user->hlj_id . ":LastUpdated";
        $updateTimeString = strtotime(date('Y-m-d H:i:s'));
        return Cache::forever($key, $updateTimeString);

    }

    public function updateUserTable($user, $authUser)
    {
        $data = [];
        $data['openid'] = $authUser['openid'];
        $data['nickname'] = isset($authUser['nickname']) ? $authUser['nickname'] : '';
        $data['sex'] = isset($authUser['sex']) ? $authUser['sex'] : '';
        $data['province'] = isset($authUser['province']) ? $authUser['province'] : '';
        $data['city'] = isset($authUser['city']) ? $authUser['city'] : '';
        $data['country'] = isset($authUser['country']) ? $authUser['country'] : '';
        $data['headimgurl'] = isset($authUser['headimgurl']) ? $authUser['headimgurl'] : '';
        $data['privilege'] = isset($authUser['privilege']) ? json_encode($authUser['privilege']) : '';
        $data['unionid'] = isset($authUser['unionid']) ? $authUser['unionid'] : ''; // unionid，App时使用
        return $user->update($data);
    }
}
