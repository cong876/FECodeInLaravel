<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Pingpp\Charge;
use Pingpp\Pingpp;

class TestRealPayController extends Controller
{
    public function getPayPage() {
        return view('realPayPage');
    }

    public function postCharge(Request $request) {
        $extra = null;
        // 渠道附加参数
        switch ($request['channel']) {
            case 'alipay_wap':
                $extra = array('success_url' => url('payment/paidSucceed'));
                break;
            case 'wx_pub':
                $extra = array('open_id' => 'olxLuv7ftcxC48-YGe6go_E-0FMo');
        }

        Pingpp::setApiKey('sk_live_Hp29kqS82OBh2iQxkdupB4l6');
        $charge = Charge::create(
            array(
                'order_no' => '123321456654',
                'app' => array('id' => 'app_WzDmrT0CC8G0HSen'),
                'channel' => $request['channel'],
                'amount' => $request['amount'],
                'client_ip' => $request->ip(),
                'currency' => 'cny',
                'subject' => 'I am testing',
                'body' => 'Default Null',
                'extra' => $extra,
            )
        );
        return $charge;
    }
    public function getQR(Request $request) {
        Pingpp::setApiKey('sk_live_Hp29kqS82OBh2iQxkdupB4l6');
        $charge = Charge::create(
            array(
                'order_no' => 'yeyego12345',
                'app' => array('id' => 'app_WzDmrT0CC8G0HSen'),
                'channel' => 'wx_pub_qr',
                'amount' => 100,
                'client_ip' => $request->ip(),
                'currency' => 'cny',
                'subject' => 'I am testing QR',
                'body' => 'Null',
                'extra' => array('product_id' => '334455'),
            )
        );
        return $charge;
    }
}
