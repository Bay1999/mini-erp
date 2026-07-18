<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesHeader extends Model
{
    protected $table = 'sales_headers';
    protected $primaryKey = 'sales_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'sales_code',
        'date',
        'grand_total',
    ];

    protected $appends = ['status'];

    public function details(): HasMany
    {
        return $this->hasMany(SalesDetail::class, 'sales_code', 'sales_code');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(\App\Models\Payment::class, 'sales_code', 'sales_code');
    }

    public function getStatusAttribute(): string
    {
        $paidAmount = $this->relationLoaded('payments')
            ? $this->payments->sum('amount')
            : $this->payments()->sum('amount');

        $grandTotal = (float) $this->grand_total;

        if ($grandTotal <= 0) {
            return 'unpaid';
        }

        if ($paidAmount >= $grandTotal) {
            return 'paid';
        } elseif ($paidAmount > 0) {
            return 'partially_paid';
        } else {
            return 'unpaid';
        }
    }
}
