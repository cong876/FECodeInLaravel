@foreach($subOrders as $subOrder)
    <?php if($subOrder->withdraw_state == 0) {
        $suborder = $subOrder;
    ?>
    <li>
        <table>
            <tbody>
            <tr>

                <td><p><span>订单号：</span><span class="order-id">{{$suborder->sub_order_number}}</span></p><p><span class="amount">
                            {{sprintf("%.2f",$suborder->transfer_price)}}</span><span>元</span></p></td>
                <td><span class="toWithdrawals">提现</span></td>
                <?php  } ?>
            </tr>
            </tbody>
        </table>
    </li>
@endforeach

