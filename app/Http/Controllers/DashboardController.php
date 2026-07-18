<?php

namespace App\Http\Controllers;

use App\Models\Sales\SalesHeader;
use App\Models\Sales\SalesDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function data(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // 1. Widgets Calculations
        $totalTransactions = SalesHeader::whereBetween('date', [$startDate, $endDate])->count();
        $totalSales = SalesHeader::whereBetween('date', [$startDate, $endDate])->sum('grand_total');
        
        $totalQty = SalesDetail::whereHas('header', function($q) use ($startDate, $endDate) {
            $q->whereBetween('date', [$startDate, $endDate]);
        })->sum('qty');

        // 2. Chart: Monthly Sales in Rupiah (generate complete list of months with 0 default)
        $start = Carbon::parse($startDate)->startOfMonth();
        $end = Carbon::parse($endDate)->startOfMonth();

        $monthsList = [];
        $temp = clone $start;
        while ($temp->lte($end)) {
            $monthsList[$temp->format('Y-m')] = [
                'label' => $temp->format('M Y'),
                'total' => 0.0
            ];
            $temp->addMonth();
        }

        $monthlySalesRaw = SalesHeader::selectRaw("DATE_FORMAT(date, '%Y-%m') as month, SUM(grand_total) as total")
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('month')
            ->get();

        foreach ($monthlySalesRaw as $row) {
            if (isset($monthsList[$row->month])) {
                $monthsList[$row->month]['total'] = (float) $row->total;
            }
        }

        $chartMonthlyLabels = [];
        $chartMonthlyData = [];

        foreach ($monthsList as $monthInfo) {
            $chartMonthlyLabels[] = $monthInfo['label'];
            $chartMonthlyData[] = $monthInfo['total'];
        }

        // 3. Chart: Item Quantities
        $itemSalesRaw = SalesDetail::select('item_code', DB::raw('SUM(qty) as total_qty'))
            ->whereHas('header', function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            })
            ->groupBy('item_code')
            ->with('item')
            ->get();

        $chartItemLabels = [];
        $chartItemData = [];

        foreach ($itemSalesRaw as $row) {
            $chartItemLabels[] = $row->item->name ?? $row->item_code;
            $chartItemData[] = (int) $row->total_qty;
        }

        return response()->json([
            'success' => true,
            'widgets' => [
                'total_transactions' => number_format($totalTransactions),
                'total_sales' => 'Rp ' . number_format($totalSales, 2),
                'total_qty' => number_format($totalQty)
            ],
            'charts' => [
                'monthly' => [
                    'labels' => $chartMonthlyLabels,
                    'data' => $chartMonthlyData
                ],
                'items' => [
                    'labels' => $chartItemLabels,
                    'data' => $chartItemData
                ]
            ]
        ]);
    }
}
