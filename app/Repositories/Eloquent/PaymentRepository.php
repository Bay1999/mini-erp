<?php

namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Models\Sales\SalesHeader;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function query()
    {
        return Payment::query();
    }

    public function find(string $code): ?Payment
    {
        return Payment::with('salesHeader')->where('code', $code)->first();
    }

    public function create(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            // Lock the SalesHeader record for update to serialize concurrent payment entries
            $sales = SalesHeader::where('sales_code', $data['sales_code'])
                ->lockForUpdate()
                ->firstOrFail();

            // Calculate total payments made for this sale
            $currentPaid = Payment::where('sales_code', $data['sales_code'])->sum('amount');
            $newTotalPaid = $currentPaid + $data['amount'];

            // Enforce overpayment validation
            if ($newTotalPaid > $sales->grand_total) {
                throw new \Exception("Total payment (Rp " . number_format($newTotalPaid, 2) . ") exceeds the sales grand total (Rp " . number_format($sales->grand_total, 2) . ").");
            }

            return Payment::create($data);
        });
    }

    public function update(string $code, array $data): Payment
    {
        return DB::transaction(function () use ($code, $data) {
            $payment = Payment::where('code', $code)->firstOrFail();
            
            // Lock the SalesHeader record for update
            $sales = SalesHeader::where('sales_code', $payment->sales_code)
                ->lockForUpdate()
                ->firstOrFail();

            // Calculate current total payments excluding this payment
            $currentPaid = Payment::where('sales_code', $payment->sales_code)
                ->where('code', '!=', $code)
                ->sum('amount');
                
            $newTotalPaid = $currentPaid + $data['amount'];

            // Enforce overpayment validation
            if ($newTotalPaid > $sales->grand_total) {
                throw new \Exception("Total payment (Rp " . number_format($newTotalPaid, 2) . ") exceeds the sales grand total (Rp " . number_format($sales->grand_total, 2) . ").");
            }

            $payment->update([
                'date' => $data['date'],
                'amount' => $data['amount'],
            ]);
            
            return $payment;
        });
    }

    public function delete(string $code): bool
    {
        return DB::transaction(function () use ($code) {
            $payment = Payment::where('code', $code)->first();
            if ($payment) {
                // Lock the SalesHeader record to prevent race conditions during deletion
                SalesHeader::where('sales_code', $payment->sales_code)
                    ->lockForUpdate()
                    ->first();
                return (bool) $payment->delete();
            }
            return false;
        });
    }
}
