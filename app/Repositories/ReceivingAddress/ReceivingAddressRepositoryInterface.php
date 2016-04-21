<?php

namespace App\Repositories\ReceivingAddress;

use App\Models\ReceivingAddress;


interface ReceivingAddressRepositoryInterface
{
   public function create(array $data, $hlj_id);

   public function update(ReceivingAddress $receivingAddress, array $data);

   public function delete(ReceivingAddress $receivingAddress);

   public function getAddressDetail($hlj_id);

   public function getDefaultAddress($hlj_id);

   public function setAddressToDefault(ReceivingAddress $receivingAddress, $hlj_id);

   public function setAddressToNormal(ReceivingAddress $receivingAddress);

   public function setStatusToAvailable(ReceivingAddress $receivingAddress);

   public function setStatusToUnavailable(ReceivingAddress $receivingAddress);
}