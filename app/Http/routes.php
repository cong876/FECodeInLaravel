<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|

 */

Route::group(['prefix' => 'auth'], function () {
    Route::get('login', 'Auth\AuthController@getLogin');
    Route::get('logout', 'Auth\AuthController@getLogout');
    Route::get('update', 'Auth\AuthController@getUpdate');
    Route::post('update', 'Auth\AuthController@postUpdate');
    Route::post('login', 'Auth\AuthController@postLogin');
});

Route::group(['prefix' => 'operator'], function () {
    /*
     * 登录方法
     * ----------------------------------------------------------------------------------------------------------------
     * GET:     login                                                                           展示运营系统登录表单
     * GET:     logout                                                                          注销当前登录用户
     * POST:    login                                                                           通过表单内容登录当前用户
     */
    Route::get('login', 'Operator\OperatorAuthController@getLogin');
    Route::get('logout', 'Operator\OperatorAuthController@getLogout');
    Route::post('login', 'Operator\OperatorAuthController@postLogin');


    /*
     * 非登录方法_需求管理
     * ----------------------------------------------------------------------------------------------------------------
     * GET:     acceptRequirement                                                               运营领取需求
     * GET:     waitAccept                                                                      展示所有待领取需求
     * GET:     waitResponse                                                                    展示所有待生成商品的需求
     * GET:     waitSplit                                                                       展示所有待拆单的需求
     * GET:     waitSendPrice                                                                   展示所有待报价订单
     * GET:     getBuyer/{countryId}                                                            获取该国买手信息
     * GET:     showFinishedRequirement                                                         展示所以已完成的需求
     * GET      showClosedRequirement                                                           展示所以已关闭的需求
     * GET:     deleteItem/{requirementDetailId}                                                删除生成商品或子需求
     * GET:     generateItems/{requirementId}                                                   展示对应需求
     * GET      editRequirement/{requirementId}                                                 领取页领取按钮链接
     * GET:     invalidRequirement/{requirementId}                                              将需求置为无效
     * GET      deleteRequirement/{requirementId}                                               待拆单TAB关闭需求
     * GET:     splitOrder/{mainOrderId}                                                        点击下一步，拆单
     * GET:     getAllRequirement                                                               需求管理全部TAB
     * GET      searchRequirement                                                               需求管理查询
     * GET      getAllOrders                                                                    订单管理全部TAB
     * POST     updateOperator                                                                  更换处理人
     * POST     deleteMemo/{Id}                                                                 删除备注
     * POST     addRequirementMemo/{requirementId}                                              增加需求备注
     * POST:    createMain                                                                      生成逻辑主订单
     * POST:    generateItems/{requirementDetailId}                                             生成新商品
     * POST:    updateItem/{itemId}/requirementDetailId/{requirementDetailId}                   更新商品
     */
    Route::get('acceptRequirement/{requirementId}', 'Operator\OperatorController@acceptRequirement');
    Route::get('waitAccept', 'Operator\OperatorController@waitAccept');
    Route::get('waitResponse', 'Operator\OperatorController@waitResponse');
    Route::get('waitSplit', 'Operator\OperatorController@waitSplit');
    Route::get('waitSendPrice', 'Operator\OperatorController@waitSendPrice');
    Route::get('getBuyer/{countryId}', 'Operator\OperatorController@getBuyer');
    Route::get('showFinishedRequirement', 'Operator\OperatorController@showFinishedRequirement');
    Route::get('showClosedRequirement', 'Operator\OperatorController@showClosedRequirement');
    Route::get('deleteItem/{requirementDetailId}', 'Operator\OperatorController@deleteCreatedItem');
    Route::get('generateItems/{requirementId}', 'Operator\OperatorController@getGenerateItemsPage');
    Route::get('editRequirement/{requirementId}', 'Operator\OperatorController@editRequirement');
    Route::get('invalidRequirement/{requirementId}', 'Operator\OperatorController@invalidRequirement');
    Route::get('deleteRequirement/{requirementId}', 'Operator\OperatorController@deleteRequirement');
    Route::get('splitOrder/{mainOrderId}', 'Operator\SplitOrderController@index');
    Route::get('getAllRequirement', 'Operator\OperatorController@getAllRequirement');
    Route::get('searchRequirement', 'Operator\OperatorController@searchRequirement');
    Route::get('getAllOrders', 'Operator\OperatorController@getAllOrders');
    Route::post('deleteMemo/{Id}', 'Operator\OperatorController@deleteMemo');
    Route::post('addRequirementMemo/{requirementId}', 'Operator\OperatorController@addRequirementMemo');
    Route::post('updateOperator', 'Operator\OperatorController@updateOperator');
    Route::post('createMain', 'Operator\OperatorController@createMainOrder');
    Route::post('generateItems/{requirementDetailId}', 'Operator\OperatorController@createItem');
    Route::post('updateItem/{itemId}/requirementDetailId/{requirementDetailId}', 'Operator\OperatorController@updateCreatedItem');


    /*
     * 非登录方法_拆单界面
     * ----------------------------------------------------------------------------------------------------------------
     * GET:     deleteOrderItem/{itemId}/mainOrderId/{mainOrderId}/subOrderId/{subOrderId?}     删除商品
     * GET:     deleteSubOrder/{subOrderId}                                                     删除子订单
     * GET:     deleteMainOrder/{mainOrderId}                                                   删除逻辑主订单
     * GET:     sendPrice/{mainOrderId}                                                         发送报价给买家和买手
     * GET:     orderDetail/{mainOrderId}                                                       待付款详情页
     * POST:    dealSubOrder                              T                                     生成或更改子订单
     * POST:    updateOrderItem/{itemId}/mainOrderId/{mainOrderId}                              更新子订单或逻辑主订单商品
     * POST:    createNewItem/{mainOrderId}                                                     主订单生成新商品
     */
    Route::get('deleteOrderItem/{itemId}/mainOrderId/{mainOrderId}/subOrderId/{subOrderId?}', 'Operator\SplitOrderController@deleteCreatedItem');
    Route::get('deleteSubOrder/{subOrderId}', 'Operator\SplitOrderController@deleteSubOrder');
    Route::get('deleteMainOrder/{mainOrderId}', 'Operator\SplitOrderController@deleteMainOrder');
    Route::get('sendPrice/{mainOrderId}', 'Operator\SplitOrderController@sendPrice');
    Route::get('orderDetail/{mainOrderId}', 'Operator\SplitOrderController@dealOrderDetail');
    Route::post('dealSubOrder', 'Operator\SplitOrderController@dealSubOrder');
    Route::post('updateOrderItem/{itemId}/mainOrderId/{mainOrderId}', 'Operator\SplitOrderController@updateCreatedItem');
    Route::post('createNewItem/{mainOrderId}', 'Operator\SplitOrderController@createItem');


    /*
     * 非登录方法_订单管理页面
     * ----------------------------------------------------------------------------------------------------------------
     * GET:      waitPay                                                                         运营后台待付款
     * GET:      waitDelivery                                                                    运营后台待发货
     * GET:      hasDelivered                                                                    运营后台已发货
     * GET:      hasFinished                                                                     运营后台已完成
     * GET:      isAuditing                                                                      运营后台审核中
     * GET:      orderSellerAssign                                                               运营后台买手拒单待分配
     * GET:      editOffer/{subOrderId}                                                          待付款详情页
     * GET:      editDeliveryOrder/{subOrderId}                                                  待发货详情页
     * GET:      editAuditingOrder/{subOrderId}                                                  审核中详情页
     * GET:      editHasDeliveredOrder/{subOrderId}                                              已发货详情页
     * GET:      editHasFinishedOrder/{subOrderId}                                               已完成详情页
     * GET:      editSellerAssignOrder/{subOrderId}                                              拒单待分配详情页
     * GET:      orderClosed                                                                     运营后台订单已关闭页
     * GET:      cancelOrder/{orderId}                                                           运营后台交易关闭请求
     * GET:      commitToHasDelivered/{subOrderNumber}                                           审核通过至已发货
     * GET:      commitToUndelivered/{subOrderNumber}                                            审核未通过至未发货
     * GET:      commitToHasFinished/{subOrderNumber}                                            运营后台确认收货按钮
     * GET:      searchOrder                                                                     订单查询
     * POST:     addOrderMemo/{orderId}                                                          增加订单备注
     * POST:     updateSeller/{subOrderId}                                                       更改买手
     * POST:     updateOrderOperator                                                             订单状态更换处理人
     * POST:     refundAll/{sub_order_id}                                                        退全款
     * POST:     refundItem/{sub_order_id}                                                       部分退款
     * POST:     createDeliveryInfo/{sub_order_id}                                               填写物流
     * GET:      exportExcel                                                                     导出Excel订单
     */
    Route::get('waitPay', 'Operator\OperatorController@waitPay');
    Route::get('waitDelivery', 'Operator\OperatorController@waitDelivery');
    Route::get('waitDeliveryGTSeven', 'Operator\OperatorController@waitDeliveryGTSevenDays');
    Route::get('hasDelivered', 'Operator\OperatorController@hasDelivered');
    Route::get('hasSecondaryDelivered', 'Operator\OperatorController@hasSecondaryDelivered');
    Route::get('hasFinished', 'Operator\OperatorController@hasFinished');
    Route::get('isAuditing', 'Operator\OperatorController@showAuditing');
    Route::get('orderSellerAssign', 'Operator\OperatorController@orderSellerAssign');
    Route::get('editOffer/{subOrderId}', 'Operator\OperatorController@editOffer');
    Route::get('editDeliveryOrder/{subOrderId}', 'Operator\OperatorController@editDeliveryOrder');
    Route::get('editAuditingOrder/{subOrderId}', 'Operator\OperatorController@editAuditingOrder');
    Route::get('editHasDeliveredOrder/{subOrderId}', 'Operator\OperatorController@editHasDeliveredOrder');
    Route::get('editHasFinishedOrder/{subOrderId}', 'Operator\OperatorController@editHasFinishedOrder');
    Route::get('editSellerAssignOrder/{subOrderId}', 'Operator\OperatorController@editSellerAssignOrder');
    Route::get('orderClosed', 'Operator\OperatorController@getClosedOrder');
    Route::get('cancelOrder/{orderId}', 'Operator\OperatorController@cancelOrder');
    Route::get('commitToHasDelivered/{subOrderNumber}', 'Operator\OperatorController@commitToHasDelivered');
    Route::post('commitToOverseaDelivered/{subOrderNumber}', 'Operator\OperatorController@commitToOverseaDelivered');
    Route::get('commitToUndelivered/{subOrderNumber}', 'Operator\OperatorController@commitToUndelivered');
    Route::get('commitToHasFinished/{subOrderNumber}', 'Operator\OperatorController@commitToHasFinished');
    Route::get('searchOrder', 'Operator\OperatorController@searchOrder');
    Route::post('addOrderMemo/{orderId}', 'Operator\OperatorController@addOrderMemo');
    Route::post('updateSeller/{subOrderId}', 'Operator\OperatorController@updateSeller');
    Route::post('updateOrderOperator', 'Operator\OperatorController@updateOrderOperator');
    Route::post('refundAll/{sub_order_id}', 'Payment\PaymentController@postRefundInformation');
    Route::post('refundItem/{sub_order_id}', 'Payment\PaymentController@postRefundInformation');
    Route::post('createDeliveryInfo/{sub_order_id}', 'Operator\OperatorController@createDeliveryInfo');
    Route::get('exportExcel', 'Operator\OperatorController@exportExcel');
    Route::get('tagsManagement', 'Operator\OperatorController@tagsManagement');

    /*
     * 非登录方法_买手管理页面
     * ----------------------------------------------------------------------------------------------------------------
     * GET:       getSeller                                                                     展示所有买手
     * GET:       getSellerDetail/{sellerId}                                                    买手管理详情页
     * GET:       searchSeller                                                                  买手查询
     * GET:       freeSeller/{sellerId}                                                         将买手拉出小黑屋
     * GET:       updateSellerCountry/{sellerId}                                                更改买手国家
     * POST:      addSellerMemo/{sellerId}                                                      增加买手备注
     * POST:      addWX_Number/{sellerId}                                                       添加买手微信号
     * POST:      arrestSeller/{sellerId}                                                       将买手关进小黑屋
     */
    Route::get('/getSeller', 'Operator\OperatorController@getSeller');
    Route::get('/getSellerDetail/{sellerId}', 'Operator\OperatorController@getSellerDetail');
    Route::get('/searchSeller', 'Operator\OperatorController@searchSeller');
    Route::get('/freeSeller/{sellerId}', 'Operator\OperatorController@freeSeller');
    Route::get('/updateSellerCountry/{sellerId}', 'Operator\OperatorController@updateSellerCountry');
    Route::post('/addSellerMemo/{sellerId}', 'Operator\OperatorController@addSellerMemo');
    Route::post('/addWX_Number/{sellerId}', 'Operator\OperatorController@addWX_Number');
    Route::post('/arrestSeller/{sellerId}', 'Operator\OperatorController@arrestSeller');

    /*
     * 非登录方法_资金管理页面
     * ----------------------------------------------------------------------------------------------------------------
     * GET:      waitEnsureCapital                                                              打款管理待确认TAB
     * GET:      waitTransfer                                                                   打款管理待打款TAB
     * GET:      hasTransferred                                                                 打款管理已打款TAB
     * GET:      allCapital                                                                     打款管理全部TAB
     * GET:      hasTransfer/{subOrderId}/{payment_id}                                          打款管理打款请求
     * GET:      searchWithdraw                                                                 打款管理查询
     * POST:     ensureCapital                                                                  打款管理确认打款金额请求
     */
    Route::get('/waitEnsureCapital', 'Operator\OperatorController@waitEnsureCapital');
    Route::get('/waitTransfer', 'Operator\OperatorController@waitTransfer');
    Route::get('/hasTransferred', 'Operator\OperatorController@hasTransferred');
    Route::get('/allCapital', 'Operator\OperatorController@allCapital');
    Route::get('hasTransfer/{subOrderId}/{payment_id}', 'Operator\OperatorController@hasTransfer');
    Route::get('searchWithdraw', 'Operator\OperatorController@searchWithdraw');
    Route::post('/ensureCapital', 'Operator\OperatorController@ensureCapital');

    /*
     * 非登录方法_买家管理页面
     * ----------------------------------------------------------------------------------------------------------------
     * GET:      buyerManagement                                                                买家管理
     * GET:      buyerManagementDetail/{buyerId}                                                买家管理详情页
     * GET:      searchBuyer                                                                    买家管理查询
     * POST:     addBuyerMemo/{buyerId}                                                         买家管理增加买家备注
     * POST:     addBuyerWX_Number/{buyerId}                                                    买家管理增加买家微信号
     */
    Route::get('/buyerManagement', 'Operator\OperatorController@buyerManagement');
    Route::get('/buyerManagementDetail/{buyerId}', 'Operator\OperatorController@buyerManagementDetail');
    Route::get('/searchBuyer', 'Operator\OperatorController@searchBuyer');
    Route::post('/addBuyerMemo/{buyerId}', 'Operator\OperatorController@addBuyerMemo');
    Route::post('/addBuyerWX_Number/{buyerId}', 'Operator\OperatorController@addWX_Number');

    /*
     * 非登录方法_活动管理页面
     * ----------------------------------------------------------------------------------------------------------------
     * GET:      allActivities                                                                   活动管理全部TAB
     * GET:      allSubjectActivities                                                            活动管理主题性活动TAB
     * GET:      allPeriodActivities                                                             活动管理周期性活动TAB
     * GET:      activitiesManagementDetail/{activityId}                                         活动管理详情页
     * GET:      deleteActivity/{activityId}                                                     删除活动
     * GET:      updateActivityItem                                                              更新活动商品
     * GET:      deleteActivityItem/{itemId}                                                     删除活动商品
     * GET:      updateActivityTitle/{activityId}                                                更新活动标题及日期
     * GET:      updateActivityInfo/{activityId}                                                 更新活动信息
     * GET:      toPublish/{activityId}                                                          发布活动
     * GET:      activitySearch                                                                  查询当日活动
     * GET:      getGoldActivity                                                                 进入运营后台金币活动页面
     * GET:      updateGoldItem                                                                  更新金币商品
     * GET:      deleteGoldItem/{itemId}                                                         删除金币商品
     * GET:      publishGoldItem/{itemId}                                                        发布金币商品到线上
     * GET:      cancelPublishGoldItem/{itemId}                                                  取消金币商品发布
     * POST:     createActivity                                                                  创建新活动
     * POST:     createActivityItem                                                              生成活动商品
     * POST:     createGoldItem                                                                  生成金币商品
     */
    Route::get('/allActivities', 'Operator\ActivityController@allActivities');
    Route::get('/allSubjectActivities', 'Operator\ActivityController@allSubjectActivities');
    Route::get('/allPeriodActivities', 'Operator\ActivityController@allPeriodActivities');
    Route::get('/activitiesManagementDetail/{activityId}', 'Operator\ActivityController@activityManagementDetail');
    Route::get('/deleteActivity/{activityId}', 'Operator\ActivityController@deleteActivity');
    Route::get('/updateActivityItem', 'Operator\ActivityController@updateItem');
    Route::get('/deleteActivityItem/{activityId}/{itemId}', 'Operator\ActivityController@deleteItem');
    Route::get('/updateActivityTitle/{activityId}', 'Operator\ActivityController@updateActivityTitle');
    Route::get('/updateActivityInfo/{activityId}', 'Operator\ActivityController@updateActivityInfo');
    Route::get('/toPublish/{activityId}', 'Operator\ActivityController@toPublish');
    Route::get('/activitySearch', 'Operator\ActivityController@activitySearch');
    Route::get('/getGoldActivity', 'Operator\ActivityController@getGoldItem');
    Route::get('/getLuckyBagActivity', 'Operator\ActivityController@getLuckyBagItem');
    Route::get('/updateLuckyBagItem', 'Operator\ActivityController@updateLuckyBagItem');
    Route::get('/deleteLuckyBagItem/{itemId}', 'Operator\ActivityController@deleteLuckyBagItem');
    Route::get('/publishLuckyBagItem/{itemId}', 'Operator\ActivityController@publishLuckyBagItem');
    Route::get('/cancelPublishLuckyBagItem/{itemId}', 'Operator\ActivityController@cancelPublishLuckyBagItem');
    Route::get('/updateGoldItem', 'Operator\ActivityController@updateGoldItem');
    Route::get('/deleteGoldItem/{itemId}', 'Operator\ActivityController@deleteGoldItem');
    Route::get('/publishGoldItem/{itemId}', 'Operator\ActivityController@publishGoldItem');
    Route::get('/cancelPublishGoldItem/{itemId}', 'Operator\ActivityController@cancelPublishGoldItem');
    Route::post('/createActivity', 'Operator\ActivityController@createActivity');
    Route::post('/createActivityItem', 'Operator\ActivityController@createItem');
    Route::post('/createGoldItem', 'Operator\ActivityController@createGoldItem');
    Route::post('/createLuckyBagItem', 'Operator\ActivityController@createLuckyBagItem');
    Route::get('/publishGroupItem/{itemId}', 'Operator\ActivityController@publishGroupItem');
    Route::get('/cancelGroupItem/{itemId}', 'Operator\ActivityController@cancelGroupItem');
    Route::get('refreshActivity/{activityId}', 'Operator\ActivityController@refresh');
    Route::get('itemShortUrl/{itemId}', 'Operator\ActivityController@getShortUrl');

});

Route::group(['prefix' => 'seller'], function () {
    Route::get('login', 'Seller\SellerAuthController@getLogin');
    Route::get('logout', 'Seller\SellerAuthController@getLogout');
    Route::post('login', 'Seller\SellerAuthController@postLogin');
    Route::post('createPayment', 'Seller\SellerController@createPayment');
    Route::post('updatePayment', 'Seller\SellerController@updatePayment');
    Route::post('createSeller', 'Seller\SellerRegistryController@createSeller');
    Route::post('setToDefault', 'Seller\SellerController@setToDefault');
    Route::post('authMobile', 'Seller\SellerRegistryController@authMobile');
    Route::post('updateMobile', 'Seller\SellerRegistryController@updateMobile');
    Route::post('updateEmail', 'Seller\SellerRegistryController@updateEmail');
    Route::post('createDeliveryInfo/{subOrderNumber}', 'Seller\SellerController@createDeliveryInfo');
    Route::get('deletePayment/{payment_id}', 'Seller\SellerController@deletePayment');
    Route::get('management', 'Seller\SellerController@index');
    Route::get('toggleToReceived', 'Seller\SellerController@ToggleToReceived');
    Route::get('toggleToNeedToDelivery', 'Seller\SellerController@ToggleToNeedToDelivery');
    Route::get('toggleToAuditing', 'Seller\SellerController@ToggleToAuditing');
    Route::get('toggleToWaitRevenue', 'Seller\SellerController@ToggleToWaitRevenue');
    Route::get('toggleToRevenue', 'Seller\SellerController@ToggleToRevenue');
    Route::get('toggleToWithdraw', 'Seller\SellerController@ToggleToWithdraw');
    Route::get('toggleToHasDelivered', 'Seller\SellerController@ToggleToHasDelivered');
    Route::get('toggleToHasFinished', 'Seller\SellerController@ToggleToHasFinished');
    Route::get('applyWithdraw/{subOrderNumber}', 'Seller\SellerController@applyWithdraw');
    Route::get('cancelSeller/{subOrderNumber}', 'Seller\SellerController@cancelSeller');
    Route::get('register', 'Seller\SellerRegistryController@register');
    Route::get('getVerifyCode/{zone}/{mobile}', 'Seller\SellerRegistryController@getVerifySMS');
    Route::get('verifyCode/{zone}/{mobile}/{code}', 'Seller\SellerRegistryController@verifyCode');
    Route::post('resetPassword', 'Seller\SellerController@resetPassword');
    Route::get('verifyMobileNumber/{mobile}', 'Seller\SellerController@verifyMobileNumber');
    Route::get('resetSecurePassword', 'Seller\SellerController@getResetPage');
});

Route::group(['prefix' => 'webHook'], function () {
    Route::post('verifyPaid', 'WebHook\OrderWebHookController@verifyPay');
    Route::post('verifyRefund', 'WebHook\OrderWebHookController@verifyRefund');
});

Route::group(['prefix' => 'payment'], function () {
    Route::get('paidSucceed', 'Payment\PaymentController@paySuccess');
    Route::post('requestPay', 'Payment\PaymentController@postPayInformation');
    Route::get('refundOrder/{sub_order_id}/query', 'Payment\PaymentController@postRefundInformation');
    Route::get('paySuccess', 'Payment\PaymentController@paySucceed');
});

Route::group(['prefix' => 'employee'], function () {
    Route::get('register', 'Employee\EmployeeController@index');
    Route::get('getAllEmployee', 'Employee\EmployeeController@getAllEmployee');
    Route::get('getEmployeeDetail/{id}', 'Employee\EmployeeController@getEmployeeDetail');
    Route::get('authMail/{email}', 'Employee\EmployeeController@authMail');
    Route::get('closeEmployee/{id}', 'Employee\EmployeeController@closeEmployee');
    Route::get('freeSeller/{id}', 'Employee\EmployeeController@freeSeller');
    Route::get('arrestSeller/{id}', 'Employee\EmployeeController@arrestSeller');
    Route::post('activateEmployee/{id}', 'Employee\EmployeeController@activateEmployee');
    Route::post('updateLevel/{id}', 'Employee\EmployeeController@updateEmployeeLevel');
    Route::post('createEmployee', 'Employee\EmployeeController@createEmployee');
});

// 首页路由
Route::get('/', 'WelcomeController@index');

// 微信路由
Route::any('/wechat', 'WechatController@serve');
Route::get('/wechat/menu', 'WechatController@setWechatMenu');
Route::get('/wechat/sub', 'WechatController@getSubscribe');
Route::get('/wechat/localPromotion', 'WechatController@makeLocalPromotionCode');
Route::get('/wechat/forwardPromotion', 'WechatController@makeForwardPromotionCode');

// 测试支付的
Route::get('testPay/Pay', 'TestRealPayController@getPayPage');
Route::post('testPay/createRealCharge', 'TestRealPayController@postCharge');
Route::get('testPay/getQR', 'TestRealPayController@getQR');


// 清除微信Token
Route::get('getWXToken', 'WechatController@getToken');
Route::get('clearToken', 'WechatController@forgetToken');

//分享转发拿金币活动路由
Route::get('accessGoldActivity', 'Gold\GoldActivityController@getGoldActivity');
Route::post('createGoldOrder', 'Gold\GoldActivityController@createGoldOrder');
Route::get('getMoreSupporters', 'Gold\GoldActivityController@getMoreSupporters');

// 订正买家数据表
Route::get('fixBuyer', 'CategoryController@fixBuyerTable');
Route::get('logUser/{hlj_id}', 'CategoryController@logAs');
Route::get('logout', 'CategoryController@logOut');

Route::get('wxh5', 'WxApp\WXAppController@index');
Route::get('singletest/wx', 'WxApp\WXAppController@singleTest');
Route::get('app/wx', 'WxApp\WXAppController@index');
Route::get('killSessions', 'LogoutAllUserController@logoutAll');

Route::get('buyerinfos', 'Operator\OperatorController@getBuyerInfo');
Route::get('killinfos', 'Operator\OperatorController@getKillInfo');
Route::get('addHistorySnapShot', 'Operator\OperatorController@addHistorySnapshot');
Route::get('addHistoryLucky', 'Operator\OperatorController@addHistoryLucky');

//Route::get('jwt', 'JWTController@getToken');
//Route::get('jwtUser', 'JWTController@getUser');

// 标签
Route::post('api/itemTags', 'Operator\OperatorItemTagController@store');
Route::put('api/itemTag/{itemTag}', 'Operator\OperatorItemTagController@update');
Route::put('api/itemTag/{itemTag}/invalid', 'Operator\OperatorItemTagController@invalid');
Route::put('api/itemTag/{itemTag}/valid', 'Operator\OperatorItemTagController@valid');
Route::delete('api/itemTag/{itemTag}', 'Operator\OperatorItemTagController@hide');

/**
 * 秒杀活动管理路由,一个秒杀活动对应一个秒杀商品,一天可以有多个秒杀活动
 *
 * @POST    api/secKills                   新建秒杀活动
 * @PUT     api/secKill/{secKill}/valid    使秒杀活动生效,不影响秒杀商品上下架
 * @PUT     api/secKill/{secKill}/invalid  使秒杀活动无效,秒杀商品随同下架
 * @PUT     api/secKill/{secKill}/putOn    使秒杀活动的商品上架,同时使得秒杀活动生效
 * @PUT     api/secKill/{secKill}/putOff   使秒杀活动的商品下架,不影响秒杀活动生效状态
 * @PUT     api/secKill/{secKill}          更新指定id的秒杀活动
 * @DELETE  api/secKill/{secKill}          删除指定id的秒杀活动
 *
 */
Route::post('api/secKills', 'Operator\OperatorSecKillController@store');
Route::put('api/secKill/{secKill}/valid', 'Operator\OperatorSecKillController@valid');
Route::put('api/secKill/{secKill}/invalid', 'Operator\OperatorSecKillController@invalid');
Route::put('api/secKill/{secKill}/putOn', 'Operator\OperatorSecKillController@putOnShelf');
Route::put('api/secKill/{secKill}/putOff', 'Operator\OperatorSecKillController@putOffShelf');
Route::put('api/secKill/{secKill}', 'Operator\OperatorSecKillController@update');
Route::delete('api/secKill/{secKill}', 'Operator\OperatorSecKillController@delete');

// 移除待处理列表的用户信息
Route::post('messages/{openid}/remove', 'Operator\OperatorController@removeUsersMessage');

// 处理资讯
Route::post('tips', 'Operator\TipCollectController@store');

Route::get('infoManagement', function() {
return view('operation.itemsManagement.informationBase');
});