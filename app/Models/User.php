<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, SoftDeletes;

    /**
     * 默认表名
     *
     * @var string
     */
    protected $table = 'users';


    /**
     * 重定义用户ID主键名
     *
     * @var string
     */

    protected $primaryKey = "hlj_id";
    protected $dates = ['deleted_at'];
    protected $fillable = ['openid', 'nickname', 'sex', 'mobile', 'wx_number', 'email',
        'province', 'city', 'country', 'headimgurl',
        'privilege', 'unionid', 'email', 'password', 'real_name','secure_password'];
    protected $hidden = ['password', 'remember_token', 'sex', 'real_name', 'wx_number', 'city'
        , 'country', 'province', 'headimgurl', 'privilege', 'unionid', 'created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function items() {
        return $this->hasMany('App\Models\Item', 'hlj_id');
    }

    public function requests() {
        return $this->hasMany('App\Models\Requirement');
    }

    public function receivingAddresses()
    {
        return $this->hasMany('App\Models\ReceivingAddress','hlj_id');
    }

    public function paymentMethods()
    {
        return $this->hasMany('App\Models\PaymentMethod', 'hlj_id');
    }

    public function buyer()
    {
        return $this->hasOne('App\Models\Buyer','hlj_id');
    }

    public function seller()
    {
        return $this->hasOne('App\Models\Seller','hlj_id');
    }

    public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'hlj_id');
    }

    public function mainOrders()
    {
        return $this->hasMany('App\Models\MainOrder', 'hlj_id');
    }

    public function subOrders()
    {
        return $this->hasMany('App\Models\SubOrder', 'buyer_id');
    }

    public function supporters()
    {
        return $this->hasMany('App\Models\Supporter','master_id');
    }

    public function golds()
    {
        return $this->hasOne('App\Models\Gold','hlj_id');
    }

    public function statements()
    {
        return $this->hasMany('App\Models\GoldStatements','hlj_id');
    }

    public function friends()
    {
        return $this->belongsToMany('App\Models\User','relations','user1_id','user2_id');
    }
}
