<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Sales\SalesHeader;

class Payment extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'date',
        'sales_code',
        'amount',
    ];

    public function salesHeader(): BelongsTo
    {
        return $this->belongsTo(SalesHeader::class, 'sales_code', 'sales_code');
    }
}
