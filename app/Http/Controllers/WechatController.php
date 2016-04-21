<?php

namespace App\Http\Controllers;

use App\Events\UserTalkedThroughWeChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Overtrue\Wechat\Server;
use Overtrue\Wechat\Menu;
use Overtrue\Wechat\MenuItem;
use App\Helper\SellerUseOnce;
use Overtrue\Wechat\Url;
use Overtrue\Wechat\Message;
use App\Models\User;
use App\Helper\WXAccessToken;
use Illuminate\Support\Facades\Cache;
use Overtrue\Wechat\User as WxUser;
use Overtrue\Wechat\Group;
use Overtrue\Wechat\QRCode;
use App\Helper\IncreaseGold;
use Overtrue\Wechat\Staff;

class WechatController extends Controller
{

    private $appId;
    private $token;
    private $encodingAESKey;
    private $secret;

    /**
     * 从.env配置中获取微信公众平台的参数
     * WechatController constructor.
     */
    public function __construct()
    {
        $this->appId = config('wx.appId');
        $this->token = config('wx.token');
        $this->encodingAESKey = config('wx.aesKey');
        $this->secret = config('wx.appSecret');
    }

    /**
     * 微信接口调用方法
     * @return mixed
     * @throws \Overtrue\Wechat\Exception
     */
    public function serve()
    {
        $server = new Server($this->appId, $this->token, $this->encodingAESKey);

        // 所有文本回复在该处处理
        $server->on('message', function ($message) {
            $userOpenId = $message->FromUserName;
            $user = DB::table('users')->where('openid', $userOpenId)->first();


            if ($message->Content == '买手注册' || $message->Content == '注册买手') {
                if ($this->isEmplyee($userOpenId)) {
                    // 引用微信短链接
                    $url = new Url($this->appId, $this->secret);
                    // 生成使用一次的注册码
                    $suo = new SellerUseOnce();
                    $code = $suo->generateRegisterCode();
                    $codeUrl = url('seller/register') . '?regCode=' . $code;
                    $shortUrl = $url->short($codeUrl);
                    // 返回供运营转发的短链接
                    return $shortUrl;
                } else {
                    return "你不是耶烨共享的员工[咒骂]";
                }
            }
            else {
                switch ($message->MsgType) {
                    case 'text':
                        $msg = $message->Content;
                        break;
                    case 'image':
                        $msg = $message->PicUrl;
                        break;
                    default:
                        $msg = "用户向公众号发送了其他类型的信息";
                }
                \Event::fire(new UserTalkedThroughWeChat($user, $msg));

            }
            return Message::make('transfer');
        });

        // 所有点击事件处理在该处处理
        $server->on('event', 'click', function ($event) {
            if ($event['EventKey'] == 'CONTACT_US') {
                $supportMembers = ['18910365102', '18911713502', '18911418802', '18910381502'];
                shuffle($supportMembers);
                $thisTime = $supportMembers[0];
                $content = "联系我们：\n1、任何问题请直接在微信中回复，红领巾客服会在第一时间应答[奋斗]\n2、客服电话：" . $thisTime . "客服妹妹声音很甜哦[害羞]";
//                $content = "联系我们：\n有问题请直接在公众号留言,客服会及时回复哒";
                return $content;
            }

            /*
            * 下方增加其他点击事件处理逻辑
            */
        });

        // 监听关注信息
        $server->on('event', 'subscribe', function ($event) {
            $userOpenId = $event->FromUserName;
            $user = User::where('openid', $userOpenId)->first();


            // 用户使用过其他功能，比如推广的小游戏已经授权过了。
            if ($user) {
                $user->is_subscribed = 1;
                $user->save();
            } else {
                // 新建用户
                $user = new User();
                $user->openid = $userOpenId;
                // 更新关注状态，并新建该用户
                $user->is_subscribed = 1;
                $user->save();
            }

            // 测试二维码分组信息
            if (isset($event->EventKey) && $event->EventKey !== []) {
                $scene_info = $event->EventKey;
                $scene_id = explode('_', $scene_info)[1];
                if ($scene_id == 33) {
                    $group = new Group($this->appId, $this->secret);
                    $group->moveUser($userOpenId, 103);
                }

                if ($scene_id == 66) {
                    $group = new Group($this->appId, $this->secret);
                    $group->moveUser($userOpenId, 105);

                    if ($master = Cache::get($userOpenId . '_forward_infos')) {
                        $master_name = null;
                        if ($tail = head(array_pop($master))) {
                            $add_info = new IncreaseGold();
                            $add_info->addGold($user, $tail[1], $tail[2]);
                            $master_name = $tail[1]->nickname;
                        }
                        if (count($master) > 0) {
                            Cache::forever($user->openid . '_forward_infos', $master);
                        } else {
                            Cache::forget($user->openid . '_forward_infos');
                        }

                        $staff = new Staff($this->appId, $this->secret);
                        $friend_info = isset($master_name) ? $master_name : '您的朋友';
                        $message = "棒棒哒！您已成功打赏#" . $friend_info . "#200颗小星星[色]\n\n点击菜单栏「个人中心-免费领！」，转发活动页面给小伙伴，就能获得属于自己的小星星噢[发呆]";
                        $message_second = "对了，我是红领巾，您的私人代购助手。\n\n点击菜单栏「帮我代购」，您想买任何国家的任何东西，我们都能在当地为您新鲜采购[奋斗]\n\n代购咨询请直接在公众号进行回复[害羞]";
                        $staff->send($message)->to($userOpenId);
                        return $message_second;

                    }
                }
            }

            // 推送团购信息，检测缓存信息
            $group_push_info = Cache::get($userOpenId . "_group_before_subscribe_info");
            $is_group = Cache::get($userOpenId . "_is_group");
            if ($is_group || $group_push_info) {

                // 用户分组
                $group = new Group($this->appId, $this->secret);
                $group->moveUser($userOpenId, 104);
                $info = json_decode($group_push_info);
                $news = Message::make('news')->items(function () use ($info) {
                    return array(
                        Message::make('news_item')
                            ->title(isset($info->title) ? $info->title : '点此参加今日团购！')
                            ->description(isset($info->description) ? $info->description : '团购商品每天10:00更新')
                            ->url($info->url)
                            ->picUrl(isset($info->imgUrl) ? $info->imgUrl : 'http://7xln8l.com2.z0.glb.qiniucdn.com/zhanfagudingbanner.jpg'),
                    );
                });
                // 使得缓存失效
                Cache::forget($userOpenId . "_group_before_subscribe_info");
                Cache::forget($userOpenId . "_is_group");

                return $news;

            }

            // 默认文案
            $content = "Hi~我是您的私人代购助手。\n\n点击菜单栏「帮我代购」，您想买任何国家的任何东西，我们都能在当地为您新鲜采购[奋斗]\n\n代购咨询请直接在公众号进行回复[害羞]";
            return $content;

        });

        // 监听取消关注事件
        $server->on('event', 'unsubscribe', function ($event) {
            $userOpenId = $event->FromUserName;
            $user = User::where('openid', $userOpenId)->first();
            $user->is_subscribed = 0;
            if ($user->save()) {
                return "";
            }

        });

        // 调用微信处理消息接口
        return $server->serve(); // 或者 return $server;
    }

    public function setWechatMenu(Request $request)
    {
        $menu = new Menu($this->appId, $this->secret);
        $type = $request->type;
        if ($type == 'all') {
            $button3 = new MenuItem("个人中心");
            $menus = array(
                new MenuItem('今日团购', 'view', 'http://www.yeyetech.net/app/wx?#/periodActivity'),
                new MenuItem('帮我代购', 'view', 'http://www.yeyetech.net/app/wx?#/buypal'),
                $button3->buttons(array(
                    new MenuItem('付款', 'view', 'http://www.yeyetech.net/app/wx?#/buyer/orders/waitPay'),
                    new MenuItem('查物流', 'view', 'http://www.yeyetech.net/app/wx?#/buyer/orders/delivered'),
                    new MenuItem('个人中心', 'view', 'http://www.yeyetech.net/app/wx?#/buyer')
                ))
            );
            try {
                $menu->set($menus);
                $menu->addConditional($menus, [
                    'group_id' => 101
                ]);// 请求微信服务器
                echo '设置成功！';
            } catch (\Exception $e) {
                echo '设置失败：' . $e->getMessage();
            }

        }

        if ($type == 'op') {
            $button3 = new MenuItem("个人中心");
            $menus = array(
                new MenuItem('今日团购', 'view', 'http://www.yeyetech.net/app/wx?#/periodActivity'),
                new MenuItem('帮我代购', 'view', 'http://www.yeyetech.net/app/wx?#/buypal'),
                $button3->buttons(array(
                    new MenuItem('付款', 'view', 'http://www.yeyetech.net/app/wx?#/buyer/orders/waitPay'),
                    new MenuItem('查物流', 'view', 'http://www.yeyetech.net/app/wx?#/buyer/orders/delivered'),
                    new MenuItem('个人中心', 'view', 'http://www.yeyetech.net/app/wx?#/buyer'),
                    new MenuItem('买手中心', 'view', 'http://www.yeyetech.net/seller/management#toDeliver')
                ))
            );
            try {
                $menu->addConditional($menus, [
                    'group_id' => 106
                ]);// 请求微信服务器
                echo '设置成功！';
            } catch (\Exception $e) {
                echo '设置失败：' . $e->getMessage();
            }
        }

        if ($type == 'seller') {
            $button2 = new MenuItem("买买买");
            $menus = array(
                new MenuItem('买手中心', 'view', 'http://www.yeyetech.net/seller/management#toDeliver'),
                $button2->buttons(array(
                    new MenuItem('今日团购', 'view', 'http://www.yeyetech.net/app/wx?#/periodActivity'),
                    new MenuItem('帮我代购', 'view', 'http://www.yeyetech.net/app/wx?#/buypal'),
                )),
                new MenuItem('个人中心', 'view', 'http://www.yeyetech.net/app/wx?#/buyer'),

            );
            try {
                $menu->addConditional($menus, [
                    'group_id' => 101
                ]);// 请求微信服务器
                echo '设置成功！';
            } catch (\Exception $e) {
                echo '设置失败：' . $e->getMessage();
            }
        }

    }

    // 整理关注，取消关注列表
    public function getSubscribe()
    {
        $appId = config('wx.appId');
        $secret = config('wx.appSecret');
        $userService = new WxUser($appId, $secret);

        $users = User::all();

        foreach ($users as $user) {
            $open_id = $user->openid;
            $user_info = $userService->get($open_id);
            if ($user_info->subscribe == 1) {
                $user->is_subscribed = 1;
                $user->save();
            }
        }
    }

    public function getToken()
    {
        $wx = new WXAccessToken();
        echo $wx->getToken();
    }

    public function forgetToken()
    {
        Cache::forget('YeYe_AccessToken');
    }

    private function isEmplyee($openId)
    {
        $user = User::where('openid', $openId)->first();
        if (!empty($user)) {
            $employee = $user->employee;
            if (!empty($employee) && $employee->op_level >= 3) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // 创建地推二维码
    public function makeLocalPromotionCode()
    {
        $qrcode = new QRCode($this->appId, $this->secret);
        $result = $qrcode->forever(33);
        echo $result->url;
    }

    // 创建转发二维码
    public function makeForwardPromotionCode()
    {
        $qrcode = new QRCode($this->appId, $this->secret);
        $result = $qrcode->forever(66);
        echo $result->url;
    }
}