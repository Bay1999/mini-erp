<?php

namespace App\Repositories\Eloquent;

use App\Models\Sales\SalesHeader;
use App\Models\Sales\SalesDetail;
use App\Repositories\Contracts\SalesRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SalesRepository implements SalesRepositoryInterface
{
    public function query()
    {
        return SalesHeader::query();
    }

    public function find(string $code): ?SalesHeader
    {
        return SalesHeader::with('details.item')->where('sales_code', $code)->first();
    }

    public function create(array $data): SalesHeader
    {
        return DB::transaction(function () use ($data) {
            $header = SalesHeader::create([
                'sales_code' => $data['sales_code'],
                'date' => $data['date'],
                'grand_total' => $data['grand_total'],
            ]);

            foreach ($data['items'] as $item) {
                SalesDetail::create([
                    'sales_code' => $header->sales_code,
                    'item_code' => $item['item_code'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);
            }

            return $header;
        });
    }

    public function update(string $code, array $data): SalesHeader
    {
        return DB::transaction(function () use ($code, $data) {
            $header = SalesHeader::where('sales_code', $code)->firstOrFail();
            
            $header->update([
                'date' => $data['date'],
                'grand_total' => $data['grand_total'],
            ]);

            // Clear existing details and recreate
            SalesDetail::where('sales_code', $code)->delete();

            foreach ($data['items'] as $item) {
                SalesDetail::create([
                    'sales_code' => $code,
                    'item_code' => $item['item_code'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);
            }

            return $header;
        });
    }

    public function delete(string $code): bool
    {
        return DB::transaction(function () use ($code) {
            $header = SalesHeader::where('sales_code', $code)->first();
            if ($header) {
                return (bool) $header->delete();
            }
            return false;
        });
    }
}
