<?php
/**
 * Created by PhpStorm.
 * User: ma0722
 * Date: 2015/6/19
 * Time: 10:25
 */
namespace App\Repositories\DetailPassiveExtra;

use App\Models\DetailPassiveExtra;

interface DetailPassiveExtraRepositoryInterface {

    public function create(array $data);

    public function updateDetailPassiveExtra(DetailPassiveExtra $detailPassiveExtra, array $data);

}