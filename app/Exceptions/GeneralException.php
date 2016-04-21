<?php
/**
 * Created by PhpStorm.
 * User: ma0722
 * Date: 2015/6/16
 * Time: 15:21
 */

namespace App\Exceptions;


class GeneralException extends \Exception{

    /**
     * @var
     */
    protected $hlj_id;
    /**
     * @var
     */
    protected $errors;

    /**
     * @param $user_id
     */
    public function setUserID($hlj_id)
    {
        $this->hlj_id = $hlj_id;
    }

    /**
     * @return mixed
     */
    public function userID()
    {
        return $this->$hlj_id;
    }

    /**
     * @param $errors
     */
    public function setValidationErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return mixed
     */
    public function validationErrors()
    {
        return $this->errors;
    }
}