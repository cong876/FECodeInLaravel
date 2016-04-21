<?php
/**
 * Created by PhpStorm.
 * User: shimeng
 * Date: 15/6/25
 * Time: 下午9:09
 */

namespace App\Repositories\PaymentMethod;

use App\Models\PaymentMethod;

interface PaymentMethodRepositoryInterface
{
    public function createPaymentMethod(array $data, $hlj_id, $channel,$identification);

    public function updatePaymentMethod(PaymentMethod $paymentMethod, array $data, $channel, $identification);

    public function getUserByPaymentMethod(PaymentMethod $paymentMethod);

    public function deletePaymentMethod(PaymentMethod $paymentMethod);

    public function setOrUpdateDefaultPaymentMethod(PaymentMethod $paymentMethod, $hlj_id);

    public function cancelDefaultPaymentMethod(PaymentMethod $paymentMethod);

    public function setStatusToAvailable(PaymentMethod $paymentMethod);

    public function setStatusToUnavailable(PaymentMethod $paymentMethod);

    public function getByPaymentId($id);

    public function getAllPaymentMethod($hlj_id);

    public function getDefaultPaymentMethod($hlj_id);

    public function getPaymentMethodWithFullDetail(PaymentMethod $paymentMethod);
    
}

