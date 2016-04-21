<?php

namespace App\Repositories\SecKill;

use App\Models\Seckill;

interface SecKillRepositoryInterface
{
    public function createSecKill(array $data, $hlj_id);

    public function createSecKillForActivity(array $data, $hlj_id, $activity_id);

    public function createSecKillForActivityWithItem(array $secKillData, array $itemData,
                                                     $publisher_id, $activity_id);

    public function updateSecKillForActivityWithItem($secKill_id, array $secKillData, array $itemData,
                                                     $publisher_id, $activity_id);

    public function setStatusToAvailable(Seckill $secKill);

    public function setStatusToUnavailable(Seckill $secKill);

    public function deleteSecKill(Seckill $secKill);

    public function putOnShelf(Seckill $secKill);

    public function putOffShelf(Seckill $secKill);

}