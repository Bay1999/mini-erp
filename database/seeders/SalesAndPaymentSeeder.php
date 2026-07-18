<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Sales\SalesHeader;
use App\Models\Sales\SalesDetail;
use App\Models\Payment;
use Carbon\Carbon;

class SalesAndPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = Item::all();
        if ($items->isEmpty()) {
            return;
        }

        // Generate 55 random sales (at least 50)
        for ($i = 1; $i <= 55; $i++) {
            // Random date between 45 days ago and today (covering this month and last month)
            $randomDays = rand(0, 45);
            $date = Carbon::now()->subDays($randomDays);
            
            // Generate unique sales code
            $dateStr = $date->format('Ymd');
            $prefix = 'SLS-' . $dateStr . '-';
            
            $lastSale = SalesHeader::where('sales_code', 'like', $prefix . '%')
                ->orderBy('sales_code', 'desc')
                ->first();
                
            if (!$lastSale) {
                $code = $prefix . '0001';
            } else {
                $lastNum = (int) substr($lastSale->sales_code, -4);
                $code = $prefix . str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
            }

            // Create SalesHeader
            $salesHeader = SalesHeader::create([
                'sales_code' => $code,
                'date' => $date->toDateString(),
                'grand_total' => 0.0,
            ]);

            // Add random items (1 to 3 items per transaction)
            $selectedItems = $items->random(rand(1, 3));
            $grandTotal = 0;

            foreach ($selectedItems as $item) {
                $qty = rand(1, 5);
                $price = $item->price;
                $total = $price * $qty;
                $grandTotal += $total;

                SalesDetail::create([
                    'sales_code' => $salesHeader->sales_code,
                    'item_code' => $item->code,
                    'price' => $price,
                    'qty' => $qty,
                    'total' => $total,
                ]);
            }

            // Update grand total
            $salesHeader->update(['grand_total' => $grandTotal]);

            // Determine payment status randomly
            // 40% paid, 30% partially_paid, 30% unpaid
            $statusRoll = rand(1, 100);
            
            if ($statusRoll <= 40) {
                // Paid (Full payment)
                $pmtPrefix = 'PMT-' . $dateStr . '-';
                $lastPmt = Payment::where('code', 'like', $pmtPrefix . '%')
                    ->orderBy('code', 'desc')
                    ->first();
                if (!$lastPmt) {
                    $pmtCode = $pmtPrefix . '0001';
                } else {
                    $lastPmtNum = (int) substr($lastPmt->code, -4);
                    $pmtCode = $pmtPrefix . str_pad($lastPmtNum + 1, 4, '0', STR_PAD_LEFT);
                }

                Payment::create([
                    'code' => $pmtCode,
                    'sales_code' => $salesHeader->sales_code,
                    'amount' => $grandTotal,
                    'date' => $date->toDateString(),
                ]);
            } elseif ($statusRoll <= 70) {
                // Partially Paid (Partial payment)
                $pmtPrefix = 'PMT-' . $dateStr . '-';
                
                // Pay a random amount between 10% and 80% of grand total
                $payAmount = round(($grandTotal * (rand(10, 80) / 100)) / 1000) * 1000;
                if ($payAmount <= 0) $payAmount = 1000;
                if ($payAmount >= $grandTotal) $payAmount = $grandTotal - 1000;

                $lastPmt = Payment::where('code', 'like', $pmtPrefix . '%')
                    ->orderBy('code', 'desc')
                    ->first();
                if (!$lastPmt) {
                    $pmtCode = $pmtPrefix . '0001';
                } else {
                    $lastPmtNum = (int) substr($lastPmt->code, -4);
                    $pmtCode = $pmtPrefix . str_pad($lastPmtNum + 1, 4, '0', STR_PAD_LEFT);
                }

                Payment::create([
                    'code' => $pmtCode,
                    'sales_code' => $salesHeader->sales_code,
                    'amount' => $payAmount,
                    'date' => $date->toDateString(),
                ]);
            }
            // Leaves the rest as unpaid (no payments created)
        }
    }
}
