<?php

namespace App\Http\Controllers;

use App\Services\SalesService;
use Illuminate\Http\Request;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\ErrorResource;
use Illuminate\Validation\ValidationException;
use Exception;

class SalesController extends Controller
{
    protected SalesService $salesService;

    public function __construct(SalesService $salesService)
    {
        $this->salesService = $salesService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->salesService->index();
        return view("pages.sales.sales-index", $data);
    }

    /**
     * Get DataTable data.
     */
    public function data()
    {
        return $this->salesService->indexDataTable();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $this->salesService->create();
        return view("pages.sales.sales-form", $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.item_code' => 'required|string|exists:items,code',
                'items.*.qty' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
            ], [
                'items.required' => 'Sales must contain at least one item.',
                'items.min' => 'Sales must contain at least one item.',
            ]);

            // Ensure unique item codes in the list
            $itemCodes = array_column($validated['items'], 'item_code');
            if (count($itemCodes) !== count(array_unique($itemCodes))) {
                throw ValidationException::withMessages([
                    'items' => 'Duplicate items are not allowed.'
                ]);
            }

            // Calculate totals
            $grandTotal = 0;
            foreach ($validated['items'] as $key => $item) {
                $total = $item['qty'] * $item['price'];
                $validated['items'][$key]['total'] = $total;
                $grandTotal += $total;
            }

            $validated['grand_total'] = $grandTotal;

            $sales = $this->salesService->store($validated);

            return new SuccessResource($sales, 'Sales record created successfully.');
        } catch (ValidationException $e) {
            return new ErrorResource($e->errors(), $e->getMessage(), 422);
        } catch (Exception $e) {
            return new ErrorResource(null, 'A server error occurred.', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $code)
    {
        $data = $this->salesService->show($code);
        if (!$data) {
            return redirect()->route('sales.index')->with('error', 'Sales record not found.');
        }
        return view("pages.sales.sales-show", $data);
    }

    public function edit(string $code)
    {
        try {
            $data = $this->salesService->edit($code);
            if (!$data) {
                return redirect()->route('sales.index')->with('error', 'Sales record not found.');
            }
            return view("pages.sales.sales-form", $data);
        } catch (Exception $e) {
            return redirect()->route('sales.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $code)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.item_code' => 'required|string|exists:items,code',
                'items.*.qty' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
            ], [
                'items.required' => 'Sales must contain at least one item.',
                'items.min' => 'Sales must contain at least one item.',
            ]);

            // Ensure unique item codes in the list
            $itemCodes = array_column($validated['items'], 'item_code');
            if (count($itemCodes) !== count(array_unique($itemCodes))) {
                throw ValidationException::withMessages([
                    'items' => 'Duplicate items are not allowed.'
                ]);
            }

            // Calculate totals
            $grandTotal = 0;
            foreach ($validated['items'] as $key => $item) {
                $total = $item['qty'] * $item['price'];
                $validated['items'][$key]['total'] = $total;
                $grandTotal += $total;
            }

            $validated['grand_total'] = $grandTotal;

            $sales = $this->salesService->update($code, $validated);

            return new SuccessResource($sales, 'Sales record updated successfully.');
        } catch (ValidationException $e) {
            return new ErrorResource($e->errors(), $e->getMessage(), 422);
        } catch (Exception $e) {
            return new ErrorResource(null, 'A server error occurred.', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $code)
    {
        try {
            $deleted = $this->salesService->delete($code);
            if ($deleted) {
                return new SuccessResource(null, 'Sales record deleted successfully.');
            }
            return new ErrorResource(null, 'Sales record not found.', 404);
        } catch (Exception $e) {
            return new ErrorResource(null, 'A server error occurred.', 500);
        }
    }
}
