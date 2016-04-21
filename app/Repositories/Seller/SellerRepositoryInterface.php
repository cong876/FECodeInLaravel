<?php

namespace App\Repositories\Seller;

use App\Models\Seller;

interface SellerRepositoryInterface
{

    public function createSellerInfo(array $data, $hlj_id);

    public function showAllSellerInfo();

    public function getSellerByCountry($country_id);

    public function showSellerInfo($id);

    public function updateSellerInfo(Seller $seller, array $data);

    public function deleteSellerInfo($id);

    public function setStatusToAvailable(Seller $seller);

    public function setStatusToUnavailable(Seller $seller);

    public function getAllAvailableSellersWithPaginate($page);
}