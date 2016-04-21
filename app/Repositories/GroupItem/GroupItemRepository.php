<?php
/**
 * Created by PhpStorm.
 * User: shimeng
 * Date: 15/10/17
 * Time: 下午5:19
 */
namespace App\Repositories\GroupItem;

use App\Repositories\BaseRepository;
use App\Models\GroupItem;

class GroupItemRepository extends BaseRepository implements GroupItemRepositoryInterface {

    protected $model;

    public function __construct(GroupItem $model)
    {
        $this->model = $model;
    }

    public function createGroupItem(array $data)
    {
        $group_item = $this->model->create($data);
        return $group_item;
    }
}