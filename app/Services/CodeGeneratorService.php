<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Sales\SalesHeader;
use App\Models\Payment;
use Carbon\Carbon;

class CodeGeneratorService
{
    public function generateItemCode(): string
    {
        $prefix = 'ITM-';
        
        $lastItem = Item::where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        if (!$lastItem) {
            $nextNumber = '0001';
        } else {
            $lastNumber = (int) substr($lastItem->code, strlen($prefix));
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        return $prefix . $nextNumber;
    }

    public function generateSalesCode(): string
    {
        $today = Carbon::today()->format('Ymd');
        $prefix = 'SLS-' . $today . '-';

        $lastSale = SalesHeader::where('sales_code', 'like', $prefix . '%')
            ->orderBy('sales_code', 'desc')
            ->first();

        if (!$lastSale) {
            $nextNumber = '0001';
        } else {
            $lastNumber = (int) substr($lastSale->sales_code, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        return $prefix . $nextNumber;
    }

    public function generatePaymentCode(): string
    {
        $today = Carbon::today()->format('Ymd');
        $prefix = 'PMT-' . $today . '-';

        $lastPayment = Payment::where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        if (!$lastPayment) {
            $nextNumber = '0001';
        } else {
            $lastNumber = (int) substr($lastPayment->code, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        return $prefix . $nextNumber;
    }
}
