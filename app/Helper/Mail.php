<?php
/**
 * Created by PhpStorm.
 * User: shimeng
 * Date: 15/9/9
 * Time: 下午5:33
 */
namespace App\Helper;

use App\Http\Requests;

class Mail {

    /*
     * 买家付款发给买手催发货
     */
    public static function MailToSellerForDeliver($mail,$order_number,$item_title, $price, $payment_time,$name,$mobile,$receiving_address,$zip_code) {

        $API_USER = 'YeYeTech_Notify';
        $API_KEY = '9fyh416OvSFhXYEY';
        $ch = curl_init();
        $vars = json_encode( array("to" => $mail,
                "sub" => array("%order_number%"=>$order_number,
                    "%item_title%" => $item_title,"%order_price%"=> $price,
                    "%payment_time%"=>$payment_time, "%name%"=> $name,
                    "%mobile%"=>$mobile,"%receiving_address%"=>$receiving_address,
                    "%zip_code%" => $zip_code
                )
            )
        );

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, 'http://sendcloud.sohu.com/webapi/mail.send_template.json');

        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'api_user' => $API_USER, # 使用api_user和api_key进行验证
            'api_key' => $API_KEY,
            'from' => 'service@yeyetech.net',
            'fromname' => '红领巾小助手',
            'use_maillist' => 'false',
            'substitution_vars' => $vars,
            'template_invoke_name' => 'delivery_notify',
            'subject' => '红领巾通知：买家已付款，请发货',
            'html' => "欢迎使用红领巾",
//            'files' => '@./test.txt'
        ));

        $result = curl_exec($ch);

        if($result === false) {
            echo curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }

    /*
     * 运营拆单发送报价催买家付款
     */
    public static function MailToBuyerForPay($mail,$item_title) {

        $API_USER = 'YeYeTech_Notify';
        $API_KEY = '9fyh416OvSFhXYEY';
        $ch = curl_init();
        $vars = json_encode( array("to" => $mail,
                "sub" => array(
                    "%item_title%" => $item_title,
                )
            )
        );

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, 'http://sendcloud.sohu.com/webapi/mail.send_template.json');

        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'api_user' => $API_USER, # 使用api_user和api_key进行验证
            'api_key' => $API_KEY,
            'from' => 'service@yeyetech.net',
            'fromname' => '红领巾小助手',
            'use_maillist' => 'false',
            'substitution_vars' => $vars,
            'template_invoke_name' => 'payment_notify',
            'subject' => '红领巾通知：您的代购商品确认有货，请尽快付款',
            'html' => "欢迎使用红领巾",
//            'files' => '@./test.txt'
        ));

        $result = curl_exec($ch);

        if($result === false) {
            echo curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }

    /*
     * 买手欢迎邮件
     */
    public static function MailToSellerForRegistrySuccessfully($mail) {

        $API_USER = 'YeYeTech_Notify';
        $API_KEY = '9fyh416OvSFhXYEY';
        $url = 'http://sendcloud.sohu.com/webapi/mail.send_template.json';

        $vars = json_encode( array("to" => $mail,
            )
        );

        $param = array(
            'api_user' => $API_USER, # 使用api_user和api_key进行验证
            'api_key' => $API_KEY,
            'from' => 'service@yeyetech.net',
            'fromname' => '红领巾小助手',
            'use_maillist' => 'true',
            'substitution_vars' => $vars,
            'template_invoke_name' => 'seller_template',
            'subject' => '红领巾通知：恭喜成为红领巾买手!',
            'html' => "欢迎使用红领巾",
//            'files' => '@./test.txt'
        );

        $data = http_build_query($param);

        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $data
            ));
        $context  = stream_context_create($options);
        $result = file_get_contents($url, FILE_TEXT, $context);

        return $result;
    }

   /*
    * 买家欢迎邮件
    */
    public static function MailToBuyerForRegistrySuccessfully($mail) {

        $API_USER = 'YeYeTech_Notify';
        $API_KEY = '9fyh416OvSFhXYEY';
        $url = 'http://sendcloud.sohu.com/webapi/mail.send_template.json';

        $vars = json_encode( array("to" => $mail,
            )
        );

        $param = array(
            'api_user' => $API_USER, # 使用api_user和api_key进行验证
            'api_key' => $API_KEY,
            'from' => 'service@yeyetech.net',
            'fromname' => '红领巾小助手',
            'use_maillist' => 'true',
            'substitution_vars' => $vars,
            'template_invoke_name' => 'buyer_template',
            'subject' => '红领巾通知：欢迎注册红领巾，快来看购物攻略，开启你的海外代购之路！',
            'html' => "欢迎使用红领巾",
//            'files' => '@./test.txt'
        );

        $data = http_build_query($param);

        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $data
            ));
        $context  = stream_context_create($options);
        $result = file_get_contents($url, FILE_TEXT, $context);

        return $result;
    }


}

