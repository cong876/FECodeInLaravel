<?php
/**
 * Created by PhpStorm.
 * User: caolixiang
 * Date: 15/6/15
 * Time: 下午12:05
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Sku extends Model
{
    protected $primaryKey = 'sku_id';
    protected $fillable = ['item_id', 'sku_spec', 'sku_price', 'sku_inventory', 'pic_urls'];
    protected $casts = [
        'pic_urls' => 'array',
    ];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item()
    {
        return $this->belongsTo('App\Models\Item','item_id');
    }

    public function modifySkuInventory($count = -1)
    {
        $this->sku_inventory += $count;
        return $this->save();

    }
}