<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Item;

class SalesDetail extends Model
{
    protected $table = 'sales_details';
    public $incrementing = false;

    protected $fillable = [
        'sales_code',
        'item_code',
        'price',
        'qty',
        'total',
    ];

    public function header(): BelongsTo
    {
        return $this->belongsTo(SalesHeader::class, 'sales_code', 'sales_code');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_code', 'code');
    }
}
