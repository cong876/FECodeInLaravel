<?php


namespace App\Repositories\ItemTag;

use App\Models\ItemTag;
use App\Repositories\BaseRepository;

class ItemTagRepository extends BaseRepository implements ItemTagRepositoryInterface
{
    protected $model;

    public function __construct(ItemTag $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(ItemTag $item_tag, array $data)
    {
        return $item_tag->update($data);
    }

    public function delete(ItemTag $item_tag)
    {
        return $item_tag->update([
            'hide' => 1,
            'is_available' => 0
        ]);
    }

    public function invalid(ItemTag $item_tag)
    {
        return $item_tag->update([
            'is_available' => 0
        ]);
    }

    public function valid(ItemTag $item_tag)
    {
        return $item_tag->update([
            'is_available' => 1
        ]);
    }
}