<?php

namespace App\Repositories\Sku;


use App\Models\Sku;

interface SkuRepositoryInterface
{
    public function create(array $data);

    public function updateSku(Sku $sku, array $data);

}