<?php
/**
 * Created by PhpStorm.
 * User: ma0722
 * Date: 2015/6/23
 * Time: 14:29
 */

namespace App\Http\Requests;


use App\Models\User;
use App\Repositories\User\UserRepository;

class RequirementRequest extends Request{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

        $userRepository = new UserRepository(new User);
        $user = parent::user();
        if($userRepository->checkUserInfoIntegrity($user->openid)){
            return response()->view('fillMobile');
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

        ];
    }


}