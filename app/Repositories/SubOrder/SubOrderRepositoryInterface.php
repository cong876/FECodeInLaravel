<?php

namespace App\Repositories\SubOrder;

use App\Models\SubOrder;

interface SubOrderRepositoryInterface
{
    public function createSubOrder(array $data);

    public function updateSubOrder(SubOrder $subOrder, array $data);

    public function deleteSubOrder(SubOrder $subOrder);

    public function deleteSubOrderByUser(SubOrder $subOrder);

    public function getAllWaitOfferOrderWithPaginate($pageCount);

    public function getAllWaitPayOrderWithPaginate($pageCount);

    public function getAllWaitOfferOrderWithPaginateByEmployeeId();

    public function getAllWaitDeliveryOrderWithPaginate($pageCount);

    public function getAllClosedOrderWithPaginate($pageCount);

    public function getAllHasDeliveredOrderWithPaginate($pageCount);

    public function getAllHasFinishedOrderWithPaginate($pageCount);

    public function getAllAuditingOrderWithPaginate($pageCount);

    public function getAllSellerAssignOrderWithPaginate($pageCount);

    public function createOrUpdateSubOrderBidSnapshot(SubOrder $subOrder);

    public function createOrUpdateSubOrderPaidSnapshot(SubOrder $subOrder);

    public function hideFinishedSubOrder(SubOrder $subOrder);
}