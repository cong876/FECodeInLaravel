<?php

namespace App\Repositories\GroupItem;

use App\Models\GroupItem;

interface GroupItemRepositoryInterface {

    public function createGroupItem(array $data);
}