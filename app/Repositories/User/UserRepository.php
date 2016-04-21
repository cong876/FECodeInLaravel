<?php namespace App\Repositories\User;

use App\Models\User;
use App\Models\Buyer;
use App\Repositories\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{


    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $input
     * @param App /Models/User $user
     * @return bool
     */
    public function create(array $data)
    {
       return $user = $this->model->create($data);
    }

    /**
     * @param User $user
     * @param $data
     * @return bool|int
     */
    public function updateInfo(User $user, array $data)
    {
        return $user->update($data);
    }

    /**
     * @param Integer $openid
     * @return mixed
     */
    public function findUserByOpenid($openid)
    {

    }

    /**
     * @param Integer $hlj_id
     * @return mixed
     */
    public function findUserByhlj_id($hlj_id)
    {

    }

    /**
     * @param Integer $openid
     * @return bool
     */
    public function deleteUserByOpenId($openid)
    {

    }


    /*
     *@param Integer $openId
     * @return bool
     */
    public function checkUserInfoIntegrity($openId)
    {
        return true;
    }

    /*
     * @param Integer $openid
     * @return mixed
     */
    public function getBuyerIdByOpenid($openid)
    {

    }

    /*
     * @param Integer $openid
     * @return mixed
     */
    public function getSellerIdByOpenid($openid)
    {

    }

    /*
     * @param App/Models/User user
     * @return bool
     */
    public function sendConfirmationEmail($user)
    {

    }

    public function createBuyerAccount(User $user)
    {
        $buyer = $user->buyer()->create([
            "hlj_id" => $user['hlj_id']
        ]);
        return ($buyer)? true : false;
    }

}