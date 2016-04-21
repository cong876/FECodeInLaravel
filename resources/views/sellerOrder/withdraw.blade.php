@foreach($subOrders as $subOrder)
    <?php

    if ($subOrder->withdraw_state == 3)
    {
        $withdraw_state = '提现成功';
        $payment_id = $subOrder->payment_methods_id;
        $payment = App\Models\PaymentMethod::withTrashed()->where('payment_methods_id', $payment_id)->first();
        $account = $payment->identification;
        $state = '(实际打款账户)';
        if ($payment->channel == 1) {
            $channel = '银行卡';
            $identification = str_replace(substr($account, 0, strlen($account) - 4), str_repeat('*', strlen($account) - 4), $account);
        } else {
            $channel = '支付宝';
            if (preg_match("/^1\d{10}$/", $account)) {
                $identification = str_replace(substr($account, 3, 4), str_repeat('*', 4), $account);
            } else {
                $identification = $account;
            }
        }
    }
    else
    {
        if (!(App\Models\PaymentMethod::where('hlj_id', $hlj_id)->first()))
        {

            $channel = ' ';
            $identification = '请添加提现方式以确保成功提现';
            $state = ' ';
            $withdraw_state = '正在提现';
        }
        else
        {
            $withdraw_state = '正在提现';
            $payment = App\Models\PaymentMethod::where('hlj_id', $hlj_id)->where('is_default', 1)->first();
            $account = $payment->identification;
            $state = '(默认提现方式)';

            if ($payment->channel == 1) {
                $channel = '银行卡';
                $identification = str_replace(substr($account, 0, strlen($account) - 4), str_repeat('*', strlen($account) - 4), $account);
            } else {
                $channel = '支付宝';
                if (preg_match("/^1\d{10}$/", $account)) {
                    $identification = str_replace(substr($account, 3, 4), str_repeat('*', 4), $account);
                } else {
                    $identification = $account;
                }
            }
        }
    }

    $price = $subOrder->transfer_price;
    ?>
    <li>
        <table>
            <tbody>
            <tr>
                <td><span>订单号：</span><span>{{$subOrder->sub_order_number}}</span></td>
                <td><span>{{$withdraw_state}}</span></td>
            </tr>
            <tr>
                <td><span class="payMethod">{{$channel}}{{$state}}</span><br/><span>{{$identification}}</span></td>
                <td><span class="amount">{{sprintf("%.2f", $price)}}</span><span>元</span></td>
            </tr>
            </tbody>
        </table>
    </li>
@endforeach