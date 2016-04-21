<?php
/**
 * Created by PhpStorm.
 * User: shimeng
 * Date: 15/7/1
 * Time: 下午8:42
 */

namespace App\Repositories\RequirementMemo;

use App\Models\RequirementMemo;
use App\Repositories\BaseRepository;

class RequirementMemoRepository extends BaseRepository implements RequirementMemoRepositoryInterface
{
    protected $model;

    public function __construct(RequirementMemo $model)
    {
        $this->model = $model;
    }

    public function create(array $data, $hlj_id, $requirement_id,$state)
    {
        $data['hlj_id'] = $hlj_id;
        $data['requirement_id'] = $requirement_id;
        $data['$state'] = $state;
        return $this->model->create($data);
    }

    public function getMemoByRequirementId($requirement_id)
    {
        return $this->model->SearchMemo($requirement_id)->get();

    }
}

