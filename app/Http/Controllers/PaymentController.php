<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\ErrorResource;
use Illuminate\Validation\ValidationException;
use Exception;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->paymentService->index();
        return view("pages.payment.payment-index", $data);
    }

    /**
     * Get DataTable data.
     */
    public function data()
    {
        return $this->paymentService->indexDataTable();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $this->paymentService->create();
        return view("pages.payment.payment-form", $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'sales_code' => 'required|string|exists:sales_headers,sales_code',
                'amount' => 'required|numeric|min:0.01',
            ]);

            $payment = $this->paymentService->store($validated);

            return new SuccessResource($payment, 'Payment recorded successfully.');
        } catch (ValidationException $e) {
            return new ErrorResource($e->errors(), $e->getMessage(), 422);
        } catch (Exception $e) {
            return new ErrorResource(null, $e->getMessage(), 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $code)
    {
        $data = $this->paymentService->show($code);
        if (!$data) {
            return redirect()->route('payment.index')->with('error', 'Payment record not found.');
        }
        return view("pages.payment.payment-show", $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $code)
    {
        $data = $this->paymentService->edit($code);
        if (!$data) {
            return redirect()->route('payment.index')->with('error', 'Payment record not found.');
        }
        return view("pages.payment.payment-form", $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $code)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'amount' => 'required|numeric|min:0.01',
            ]);

            $payment = $this->paymentService->update($code, $validated);

            return new SuccessResource($payment, 'Payment updated successfully.');
        } catch (ValidationException $e) {
            return new ErrorResource($e->errors(), $e->getMessage(), 422);
        } catch (Exception $e) {
            return new ErrorResource(null, $e->getMessage(), 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $code)
    {
        try {
            $deleted = $this->paymentService->delete($code);
            if ($deleted) {
                return new SuccessResource(null, 'Payment deleted successfully.');
            }
            return new ErrorResource(null, 'Payment record not found.', 404);
        } catch (Exception $e) {
            return new ErrorResource(null, 'A server error occurred.', 500);
        }
    }
}
