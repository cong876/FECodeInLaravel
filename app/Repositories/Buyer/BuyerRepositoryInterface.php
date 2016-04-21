<?php
/**
 * Created by PhpStorm.
 * User: shimeng
 * Date: 15/6/25
 * Time: 下午6:19
 */
namespace App\Repositories\Buyer;

use App\Models\Buyer;

interface BuyerRepositoryInterface
{
    public function createBuyerInfo(array $data, $hlj_id);

    public function showAllBuyerInfo();

    public function showBuyerInfo($id);

    public function updateBuyerInfo(Buyer $buyer, array $data);

    public function deleteBuyerInfo($id);

    public function setStatusToAvailable(Buyer $buyer);

    public function setStatusToUnavailable(Buyer $buyer);

    public function getAvailableBuyersWithPaginate($page);
}