<?php namespace App\Repositories\User;

use App\Models\PaymentMethod;
use App\Models\User;

interface UserRepositoryInterface{

    /**
     * @param array $input
     * @param App/Models/User $user
     * @return bool
     */
    public function create(array $data);

    public function updateInfo(User $user,array $data);

    public function findUserByOpenid($openid);

    public function findUserByhlj_id($hlj_id);

    public function deleteUserByOpenId($openid);

    public function checkUserInfoIntegrity($openId);

    public function getBuyerIdByOpenid($openid);

    public function getSellerIdByOpenid($openid);

    public function sendConfirmationEmail($user);

    public function createBuyerAccount(User $user);


}