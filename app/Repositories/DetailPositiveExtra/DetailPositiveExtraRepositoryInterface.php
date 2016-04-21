<?php
/**
 * Created by PhpStorm.
 * User: ma0722
 * Date: 2015/6/19
 * Time: 11:26
 */

namespace App\Repositories\DetailPositiveExtra;


use App\Models\DetailPositiveExtra;

interface DetailPositiveExtraRepositoryInterface {

    public function create(array $data);

    public function updateDetailExtraPositive(DetailPositiveExtra $detailPositiveExtra, array $data);



}