<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPositiveExtra extends Model
{
    protected $primaryKey = 'detail_positive_id';

    protected $fillable = ['item_id', 'hlj_buyer_description', 'hlj_buyer_memo', 'hlj_buyer_provide_links',
    'number', 'hlj_buyer_voice_record_url', 'hlj_admin_response_information'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item() {
        return $this->belongsTo('App\Models\Item');
    }
}
