<?php
/**
 * Created by PhpStorm.
 * User: shimeng
 * Date: 15/6/25
 * Time: 下午9:09
 */

namespace App\Repositories\PaymentMethod;

use App\Models\PaymentMethod;
use App\Models\BankCardBin;
use App\Repositories\BaseRepository;

class PaymentMethodRepository extends BaseRepository implements PaymentMethodRepositoryInterface
{
    protected $model;

    public function __construct(PaymentMethod $model)
    {
        $this->model = $model;
    }

    /**
     * 创建新的提现方式
     * @param array $data
     * @param $hlj_id
     * @param $channel
     * @param $identification
     * @return bool|\Illuminate\Database\Eloquent\Model|null|static
     */
    public function createPaymentMethod(array $data, $hlj_id, $channel, $identification)
    {
        $data['hlj_id'] = $hlj_id;
        $data['channel'] = $channel;
        $data['identification'] = $identification;

        // 查找重复情况
        $current = $this->model
            ->where('identification', $identification)->first();
        if($current) {
            // 当前卡号被使用过
            return false;
        }
        else {
            // 查看是否有历史记录历史记录，软删除
            $history = $this->model
                ->onlyTrashed()
                ->where('identification', $identification)->first();
            if($history && ( $history->hlj_id == $hlj_id) ) {
                // 恢复并更新数据
                $history->restore();
                $history->account_name = $data['account_name'];
                $history->save();
                return $history;
            }
            if($history && ( $history->hlj_id != $hlj_id)) {
                return false;
            }
            else {
                // 全新的记录
                if ($channel == 1) {
                    $data['bank_card_bin_id'] = BankCardBin::getCardInfo($identification)->id;
                    return $payment = $this->model->create($data);
                } else {
                    return $payment = $this->model->create($data);
                }
            }

        }
    }

    /**
     *  更新提现方式
     *
     * @param PaymentMethod $paymentMethod
     * @param array $data
     * @param $channel
     * @param $identification
     * @return bool|int
     * @throws \Exception
     */
    public function updatePaymentMethod(PaymentMethod $paymentMethod, array $data, $channel, $identification)
    {
        $current = $this->model
            ->where('identification', $identification)->first();
        if($current && $current->hlj_id == $paymentMethod->hlj_id) {
            $current->account_name = $data['account_name'];
            return $current->save();
        }
        if($current && ( $current->hlj_id != $paymentMethod->hlj_id)) {
            return false;
        }
        $history = $this->model
            ->onlyTrashed()
            ->where('identification', $identification)->first();
        if($history && ( $history->hlj_id == $paymentMethod->hlj_id) ) {
            // 恢复并更新数据
            $history->restore();
            $history->account_name = $data['account_name'];
            $history->save();
            if($paymentMethod->is_default) {
                $this->setOrUpdateDefaultPaymentMethod($history, $history->hlj_id);
            }
            $paymentMethod->delete();
            return true;
        }
        if($history && ( $history->hlj_id != $paymentMethod->hlj_id)) {
            return false;
        }
        $data['identification'] = $identification;
        if ($channel == 1) {
            $data['bank_card_bin_id'] = BankCardBin::getCardInfo($identification)->id;
            return $paymentMethod->update($data);
        } else {
            return $paymentMethod->update($data);
        }
    }

    /**
     *
     *
     * @param PaymentMethod $paymentMethod
     * @return mixed
     */
    public function getUserByPaymentMethod(PaymentMethod $paymentMethod)
    {
        return $paymentMethod->user;
    }

    /**
     *  删除提现方式
     *
     * @param PaymentMethod $paymentMethod
     * @return bool|null
     * @throws \Exception
     */
    public function deletePaymentMethod(PaymentMethod $paymentMethod)
    {
        $paymentMethod->is_available = false;
        $paymentMethod->is_default = false;
        $paymentMethod->save();
        return $paymentMethod->delete();
    }

    /**
     * 创建或者更新默认提现方式
     *
     * @param PaymentMethod $paymentMethod
     * @param $hlj_id
     * @return bool
     */
    public function setOrUpdateDefaultPaymentMethod(PaymentMethod $paymentMethod, $hlj_id)
    {

        $default = $this->model->where('hlj_id',$hlj_id)->where('is_default', true)->first();
        if ($default) {
            $default->is_default = false;
            $default->save();
        }
        $paymentMethod->is_default = true;
        return $paymentMethod->save();
    }

    /**
     *
     * @param PaymentMethod $paymentMethod
     */
    public function cancelDefaultPaymentMethod(PaymentMethod $paymentMethod)
    {
        $paymentMethod->is_default = false;
        $paymentMethod->save();
    }

    /**
     * @param PaymentMethod $paymentMethod
     * @return bool
     */
    public function setStatusToAvailable(PaymentMethod $paymentMethod)
    {
        $paymentMethod->is_available = true;
        return $paymentMethod->save();
    }

    /**
     * @param PaymentMethod $paymentMethod
     * @return bool
     */
    public function setStatusToUnavailable(PaymentMethod $paymentMethod)
    {
        $paymentMethod->is_available = false;
        return $paymentMethod->save();
    }

    /**
     * 通过paymentID 获取payment信息
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getByPaymentId($id)
    {
        $paymentMethod = parent::getById($id);
        $identification = $paymentMethod->identification;
        if ($paymentMethod->channel == 1) {
            $paymentMethod->bankInfo;
            $paymentMethod->identification = $this->changeBankCardNumber($paymentMethod->identification);
            return $paymentMethod;
        } elseif ($paymentMethod->channel == 2) {
            if (preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/", $identification)) {
                $paymentMethod->identification = $identification;
                return $paymentMethod;
            } elseif (preg_match("/^1\d{10}$/", $identification)) {
                $paymentMethod->identification = str_replace(substr($identification, 3, 4),
                    str_repeat('*', 4), $identification);
                return $paymentMethod;
            }

        }
    }

    /**
     * 展示所有提现方式
     *
     * @param $hlj_id
     * @return array
     */
    public function getAllPaymentMethod($hlj_id)
    {
        $builder = PaymentMethod::where('hlj_id',$hlj_id)->get();
        $count = $builder->count();
        $str=[];
        for($i=0;$i<$count;$i++) {
            $temp = $builder[$i]->payment_methods_id;
            $str[] = $this->getByPaymentId($temp);
        }
        return $str;
    }

    /**
     * @param $number
     * @return mixed
     */
    private function changeBankCardNumber($number)
    {

        $hiddenLength = strlen($number) - 4;
        return str_replace(substr($number, 0, $hiddenLength), str_repeat('*', $hiddenLength), $number);
    }

    /**
     *
     * 获取默认提现方式
     * @param $hlj_id
     * @return mixed
     *
     */
    public function getDefaultPaymentMethod($hlj_id)
    {
        $count = PaymentMethod::where('hlj_id',$hlj_id)->get()->count();
        $str = $this->getAllPaymentMethod($hlj_id);
        for($i=0;$i<$count;$i++)
        {
            if($str[$i]->is_default == 1)
                return $str[$i];
        }
    }

    /**
     *
     * @param PaymentMethod $paymentMethod
     * @return PaymentMethod
     */
    public function getPaymentMethodWithFullDetail(PaymentMethod $paymentMethod)
    {
        return $paymentMethod;
    }
}


