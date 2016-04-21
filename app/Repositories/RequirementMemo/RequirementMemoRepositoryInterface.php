<?php
/**
 * Created by PhpStorm.
 * User: shimeng
 * Date: 15/7/1
 * Time: 下午8:43
 */

namespace App\Repositories\RequirementMemo;

use App\Models\RequirementMemo;

interface RequirementMemoRepositoryInterface
{
    public function create(array $data, $hlj_id, $requirement_id,$state);

    public function getMemoByRequirementId($requirement_id);
}