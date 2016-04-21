<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Seller;
use App\Models\RequirementMemo;
use App\Models\SubOrder;
use App\Models\VirtualCategory;
use App\Models\DetailPositiveExtra;
use App\Models\Item;
use App\Models\Sku;
use App\Models\ReceivingAddress;
use App\Models\PaymentMethod;
use App\Models\Buyer;
use App\Models\User;
use App\Models\ChinaRegion;
use App\Models\MainOrder;
use Illuminate\Http\Request;
use App\Repositories\Item\ItemRepositoryInterface;
use App\Repositories\VirtualCategory\VirtualCategoryRepositoryInterface;
use App\Repositories\ReceivingAddress\ReceivingAddressRepositoryInterface;
use App\Repositories\PaymentMethod\PaymentMethodRepositoryInterface;
use App\Repositories\Buyer\BuyerRepositoryInterface;
use App\Repositories\Seller\SellerRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\RequirementMemo\RequirementMemoRepositoryInterface;
use App\Repositories\SubOrder\SubOrderRepositoryInterface;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\Requirement\RequirementRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Predis;
use Cache;
use App\Repositories\SecKill\SecKillRepositoryInterface;
use XS;

class CategoryController extends Controller
{
    private $requirement;
    private $secKill;

    public function __construct(PaymentMethodRepositoryInterface $requirement,
                                SecKillRepositoryInterface $secKill)
    {
        $this->requirement = $requirement;
        $this->secKill = $secKill;
    }

    public function index()
    {
        //** test VirtualCategory **/
        //** get category **/
//        $category = $this->category->getById(1);
//        return $this->category->getCategories($category);
        //** add subCategories **/
//        $category = $this->category->getById(1);
//        $this->category->addCategories($category,[2,3]);
//        echo 'ok';
        //** update Category **/
//        $category = $this->category->getById(1);
//        $this->category->updateCategory($category,array('virtual_category_name' => 'shit'));
//        echo 'ok';
        //** delete Categories **/
//        $category = $this->category->getById(1);
//        $this->category->deleteCategories($category,array(2,3));
//        echo 'ok';
        //** get all **/
//        return $this->category->getAll();
        //** Test Receiving Address **/
        //** create **/
//        $p = $this->requirement->create(array('receiver_name' => 'demon','receiver_mobile' => '18888888888', 'receiver_zip_code' => '100000'),1);
//        return $p;
        //** update **/
//        $category = $this->category->getById(10);
//        echo 'ok';
//        $this->category->update($category,array('receiver_name' => 'fuck', 'receiver_mobile' => '13999998888'));
//        echo 'ok';
        //** delete **/
//        $category = $this->category->getById(10);
//        $this->category->delete($category);
//        echo 'ok';
        //** is default **/
//        $category = $this->requirement->getById(1);
//        $this->requirement->setAddressToDefault($category,1);
//        var_dump($category);
        //** is Normal **/
//        $category = $this->category->getById(11);
//        $this->category->setAddressToNormal($category);
//        var_dump($category);
        //** is available **/
//        $category = $this->category->getById(11);
//        $this->category->SetStatusToAvailable($category);
//        var_dump($category);
        //** is unavailable **/
//        $category = $this->category->getById(11);
//        $this->category->SetStatusToUnavailable($category);
//        var_dump($category);
        //** get Address Detail **/
//        return $this->category->getAddressDetail(5);
        //var_dump($category);
        //**  get Default Address **/
//        return $this->requirement->getDefaultAddress(1);
        //** create Payment Method **/
//        $p = $this->requirement->createPaymentMethod(array('account_name' => 'simon','channel' => '1'),2,1,'6212280004600742785');
//        return $p;
        //** update Payment Method **/
//        $category = $this->category->getById(2);
//        $this->category->updatePaymentMethod($category,array('channel'=>'2'));
//        var_dump($category);
//        //** get user by Payment Method **/
//        $category = $this->category->getById(3);
//        return $this->category->getUserByPaymentMethod($category);
        //** set Default Payment Method **/
//          $category = $this->category->getById(2);
//          $this->category->setOrUpdateDefaultPaymentMethod($category,2);
//          var_dump($category);
        //** cancel Default Payment Method **/
//        $category = $this->category->getById(1);
//        $this->category->cancelDefaultPaymentMethod($category);
        //** get By Bank Number */
//        return PaymentMethod::getByBankCardNumber();
        //** get by ID **/
//        $id =  $this->requirement->getByPaymentId(9);
//        return $id;
        //echo 'ok';
        //** delete Payment Method **/
//        $this->category->deletePaymentMethod(9);
//        return 1;
        //** getAllPaymentMethod **/
//        return $this->category->getAllPaymentMethod(2);
        //** get Default PaymentMethod **/
//         return $this->category->getDefaultPaymentMethod(2);
        //** get Payment Method with Full Detail **/
//        $category = $this->category->getById(2);
//        return $this->category->getPaymentMethodWithFullDetail($category);
        //** create BuyerInfo */
//        $this->category->createBuyerInfo(array('buyer_memo'=> 'Da Meng','buyer_success_paid'=>0),2);
//        echo 'ok';
        //** show BuyerInfo **/
//        return $this->category->showBuyerInfo(3);
        //** get UserInfoByBuyerInfo **/
//        $category = $this->category->getById(3);
//        return $this->category->getUserInfoByBuyerInfo($category);
        //** show all BuyerInfo **/
//        return $this->category->showAllBuyerInfo();
        //** create Item **/
        // var_dump(bcrypt(123456));
        // return $this->requirement->create(array('title'=>'fuck_shit'),1,null,true,array(array('sku_inventory'=>'3')),array('hlj_buyer_description'=>'shit'));
        //** update Item **/
//        $category = $this->category->getById(17);
//        $this->category->updateItem($category,array('title'=>123),null,array(),array('hlj_buyer_description'=>'321'));
        //** get requirementMemo **/
//       return $this->category->getMemoByRequirementId(2);
        //** create RequirementMemo **/
//        $this->category->create(array('content'=>'更改需求'),2,2,1);
        //** 生成需求 **/
//        return $this->requirement->create(['country_id' => 1, 'detail' => '[{"order":0,"title":"哈哈哈哈","number":"2","pic_urls":["http://ac-k1cslg2a.clouddn.com/4PDSQBF4DmOF0de8NIvXsN833QcpHali3pe8mB4D.jpg"],"description":"非常好"},{"order":1,"title":"测试啦啦啦","number":"1","pic_urls":[],"description":"松岛枫"}]'],1);
        //** getSellByCountry **/
//        return $this->requirement->getSellerByCountry(2);
        //return $p->childRegions;
//        $m=MainOrder::where('hlj_id',1)->first();
//        $time = $m->updated_at;
//        $timeNew= strtotime($time);
//        return date('Y-m-d H:i:s',$timeNew+3*24*60*60);
//     $arr=$this->requirement->getSellerByCountry(3);
//     return $arr[0]->seller_id;
//        return $this->requirement->getAllWaitPayOrderWithPaginate(15);
//        return $this->requirement->getAllWaitOfferOrderWithPaginate();


    }
    public function show($id)
    {
        $categories = $this->category->find($id);
        return $categories;
    }

//    function send_mail()
//    {
//        $url = 'http://sendcloud.sohu.com/webapi/mail.send_template.json';
//        $API_USER = 'YeYeTech_Notify';
//        $API_KEY = '9fyh416OvSFhXYEY';
//        $vars = json_encode( array("to" => array('luye@yeyetech.net','542976414@qq.com','all@yeyetech.net'),
//                "sub" => array("item_title" => Array('雅诗兰黛','薇姿'),'item_number'=> Array(2,3))
//            )
//        );
//
//
//        //不同于登录SendCloud站点的帐号，您需要登录后台创建发信子帐号，使用子帐号和密码才可以进行邮件的发送。
//        $param = array(
//            'api_user' => $API_USER,
//            'api_key' => $API_KEY,
//            'from' => '542976414@qq.com',
//            'fromname' => 'yeye',
//            'use_maillist' => 'false',
//            'substitution_vars' => $vars,
//            'template_invoke_name' => 'payment_notify',
//            //'to' => 'WSmeng666@163.com',
////            'subject' => '红领巾通知：买家已付款，请发货',
//            'subject' => '红领巾通知：您的代购商品确认有货，请尽快付款',
//            //'html' => '我要测试',
//            'resp_email_id' => 'true'
//        );
//
//        $data = http_build_query($param);
//
//        $options = array(
//            'http' => array(
//                'method' => 'POST',
//                'header' => 'Content-Type: application/x-www-form-urlencoded',
//                'content' => $data
//            ));
//        $context  = stream_context_create($options);
//        $result = file_get_contents($url, FILE_TEXT, $context);
//
//        return $result;
//
//    }

    function send_mail() {

        $API_USER = 'YeYeTech_Notify';
        $API_KEY = '9fyh416OvSFhXYEY';
        $ch = curl_init();
        $vars = json_encode( array("to" => array('luye@yeyetech.net','542976414@qq.com'),
                "sub" => array("%order_number%"=>["YE34567890222","YE232832894892"],
                    "%item_title%" => ["雅诗兰黛小棕瓶...","契尔氏洗发水300ml..."],"%order_price%"=> [300,200],
                    "%payment_time%"=>["2015-09-09 21:50:30","2015-09-09 21:50:50"], "%name%"=> ["角蛙","大萌"],
                    "%mobile%"=>["18610221388","18311329339"],"%receiving_address%"=>["五道口","五道口"],
                    "%zip_code%" => ["100018","100018"]
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
            'from' => '542976414@qq.com',
            'fromname' => 'YeYeShare',
            'use_maillist' => 'false',
            'substitution_vars' => $vars,
            'template_invoke_name' => 'delivery_notify',
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


    public function mail()
    {
        //return "hello";
      return $this->send_mail();
    }
 // echo send_mail();
//echo send_mail();
    public function fixBuyerTable()
    {
        $subOrders = SubOrder::where('ppp_status', 1)->get();
        foreach($subOrders as $sub) {
            $buyer = $sub->mainOrder->user->buyer;
            $buyer->buyer_paid_count += 1;
            $buyer->buyer_initial_paid += $sub->sub_order_price;
            $buyer->save();
        }
    }

    public function logAs($user_id) {
        $user = User::find($user_id);
        \Auth::login($user);
        return 'ok';
    }

    public function logOut() {
        \Auth::logout();
        return 'ok';
    }

    public function testCreate(){
        $this->secKill->createSecKillForActivityWithItem(
            [
            ],
            [
                'market_price' => 120,
                'operator_id' => 1,
                'postage' => 10,
                'seller_id' => 1,
                'title' => 'Good',
                'pic_urls' => ['http://www.google.com'],
                'price' => 12,
                'country_id' => 11,
                'sku_inventory' => 10,
                'description' => 'VeryVeryGood'
            ], 2, 1);

    }
}
