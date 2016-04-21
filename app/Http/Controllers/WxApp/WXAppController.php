<?php

namespace App\Http\Controllers\WxApp;

use Cache;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;

class WXAppController extends Controller
{

    function __construct()
    {
//        $this->middleware('wechatauth');
    }

    public function index()
    {
        $user = Auth::user();
        if ($this->isSoCalledNewUser($user) && $this->hasNoValidateLuckyBagSubOrder($user)) {
            $user['isNewBuyer'] = true;
        } else {
            $user['isNewBuyer'] = false;
        }
        $user['canBuySecKillItem'] = $this->canBuySecKillItem($user);
        $currentUser = [
            'hlj_id' => $user['hlj_id'],
            'nickname' => str_replace("'", "’", $user['nickname']),
            'mobile' => $user['mobile'],
            'email' => $user['email'],
            'headImageUrl' => $user['headimgurl'],
            'isNewbuyer' => $user['isNewBuyer'],
            'registered_at' => $user['created_at']->toDateTimeString(),
            'can_sec_kill' => $user['canBuySecKillItem']
        ];

        return view('wx-app.index')->with(["currentUser" => json_encode($currentUser)]);
    }

    public function singleTest()
    {
        $user = Auth::user();
        if ($this->isSoCalledNewUser($user) && $this->hasNoValidateLuckyBagSubOrder($user)) {
            $user['isNewBuyer'] = true;
        } else {
            $user['isNewBuyer'] = false;
        }
        $user['canBuySecKillItem'] = $this->canBuySecKillItem($user);
        $currentUser = [
            'hlj_id' => $user['hlj_id'],
            'nickname' => $user['nickname'],
            'mobile' => $user['mobile'],
            'email' => $user['email'],
            'headImageUrl' => $user['headimgurl'],
            'isNewbuyer' => $user['isNewBuyer'],
            'registered_at' => $user['created_at']->toDateTimeString(),
            'can_sec_kill' => $user['canBuySecKillItem']
        ];

        return view('wx-app.test')->with(["currentUser" => json_encode($currentUser)]);

    }

    /**
     * 判断用户是否为从未发生过购物行为的新用户
     * @param User $user
     * @return bool
     */
    private function isSoCalledNewUser(User $user)
    {
        if (is_null($user->buyer) || ($user->buyer && $user->buyer->buyer_initial_paid == 0)) {
            return true;
        }
        return false;
    }

    /**
     * 判断买家是否有未付款的福袋商品
     * @param User $user
     * @return bool
     */
    private function hasNoValidateLuckyBagSubOrder(User $user)
    {
        $subOrders = $user->subOrders;
        foreach ($subOrders as $subOrder) {
            if ($subOrder->order_type == 3 && $subOrder->sub_order_state == '201') {
                return false;
            }
        }
        return true;
    }

    private function canBuySecKillItem(User $user) {
        $junkier = false;
        if ($user->buyer && $user->buyer->buyer_initial_paid < 10 && $user->buyer->buyer_initial_paid != 0) {
            $junkier = true;
        }
        $successSecTokenKey = "User:".$user->hlj_id.":GetSecKillRecently";
        $getSecKillRecently = Cache::get($successSecTokenKey);
        return (!$junkier && !$getSecKillRecently);
    }
}
