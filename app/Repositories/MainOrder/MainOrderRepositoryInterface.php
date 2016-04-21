<?php

namespace App\Repositories\MainOrder;

use App\Models\MainOrder;

interface MainOrderRepositoryInterface
{

    public function createMainOrder(array $data, $hlj_id);

    public function updateMainOrder(MainOrder $mainOrder, array $data);

    public function deleteMainOrder(MainOrder $mainOrder);

    public function deleteMainOrderByUser(MainOrder $mainOrder);

    public function getMainOrderPrice(MainOrder $mainOrder);

    public function getAllWaitSendPriceOrdersWithPaginate($pageCount);

}

