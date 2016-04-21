<?php


namespace App\Repositories\ItemTag;

use App\Models\ItemTag;


interface ItemTagRepositoryInterface
{
    public function create(array $data);
    public function update(ItemTag $item_tag, array $data);
    public function delete(ItemTag $item_tag);
    public function invalid(ItemTag $item_tag);
    public function valid(ItemTag $item_tag);

}