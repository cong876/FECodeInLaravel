<?php

$api->version('v1', function ($api) {
    $api->get('getVerificationCode', 'VerificationCodeController@getSMSVerifyCode');
    $api->get('verifySMSCode', 'VerificationCodeController@verifySMSCode');

    $api->get('buyerRegister/checkMobile', 'BuyerRegisterController@checkMobileIsAvailable');
    $api->post('buyerRegister/updateAccount', 'BuyerRegisterController@updateUserInfoAndCreateBuyerRecord');

    $api->get('buyPal/getLuckyBagItem', 'IWantToBuy\LuckyBagItemController@getItem');
    $api->get('buyPal/getGroupItems', 'IWantToBuy\RecommendItemsController@getItemsInfo');

    // 需求路由
    $api->post('user/{user}/requirements', 'UserRequirement\UserRequirementController@store');
    $api->get('user/{id}/requirements', 'UserRequirement\UserRequirementController@index');
    $api->delete('user/{hljId}/requirement/{reqId}', 'UserRequirement\UserRequirementController@delete');

    $api->put('user/{user}/mobileOrEmail', 'User\UserController@updateMobileOrEmail');
    $api->post('user/{user}/addresses', 'UserReceivingAddress\UserReceivingAddressController@store');
    $api->get('user/{user}/addresses', 'UserReceivingAddress\UserReceivingAddressController@index');
    $api->get('user/{user}/address/{address}', 'UserReceivingAddress\UserReceivingAddressController@show');
    $api->put('user/{user}/address/{address}', 'UserReceivingAddress\UserReceivingAddressController@update');
    $api->delete('user/{user}/address/{address}', 'UserReceivingAddress\UserReceivingAddressController@delete');

    $api->get('user/{user}/subOrders/{state}', ['as' => 'user.suborders', 'uses' => 'UserSubOrder\UserSubOrderController@index']);
    $api->delete('user/{user}/subOrder/{subOrder}', 'UserSubOrder\UserSubOrderController@delete');
    $api->delete('user/{user}/subOrder/{subOrder}/hide', 'UserSubOrder\UserSubOrderController@hide');
    $api->post('user/{user}/subOrder/{subOrder}/received', 'UserSubOrder\UserSubOrderController@received');
    $api->post('user/{user}/subOrders', 'UserSubOrder\UserSubOrderController@store');

    // 地址
    $api->get('regions/{level}', 'ChinaRegion\ChinaRegionController@index');
    $api->get('region/{code}', 'ChinaRegion\ChinaRegionController@show');
    $api->get('region/{code}/subRegions', 'ChinaRegion\ChinaRegionController@showSubRegions');

    // 物流
    $api->get('subOrder/{id}/deliveryInfo', 'SubOrderDeliveryInfo\SubOrderDeliveryInfoController@show');

    // 团购 && 秒杀
    $api->get('activities/current', 'Activity\ActivityController@current');
    $api->get('activity/{activity}', 'Activity\ActivityController@show');
    $api->get('activity/{activity}/user/{user}/secKills', 'ActivitySecKill\ActivitySecKillController@index');
    $api->get('activities', 'Activity\ActivityController@index');

    // 秒杀提醒路由
    $api->post('secKill/{secKill}/user/{user}/remind', 'SecKill\SecKillController@remind');
    $api->post('secKill/{secKill}/user/{user}/cancelRemind', 'SecKill\SecKillController@cancelRemind');
});
