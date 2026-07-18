<?php

namespace App\Services;

use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\SalesRepositoryInterface;
use App\Services\CodeGeneratorService;

class PaymentService
{
    protected PaymentRepositoryInterface $paymentRepository;
    protected CodeGeneratorService $codeGenerator;
    protected SalesRepositoryInterface $salesRepository;

    public function __construct(
        PaymentRepositoryInterface $paymentRepository,
        CodeGeneratorService $codeGenerator,
        SalesRepositoryInterface $salesRepository
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->codeGenerator = $codeGenerator;
        $this->salesRepository = $salesRepository;
    }

    public function index()
    {
        $breadcrumbs = [
            ['label' => 'Payment', 'url' => route('payment.index')],
        ];

        return [
            'breadcrumbs' => $breadcrumbs,
        ];
    }

    public function indexDataTable()
    {
        $query = $this->paymentRepository->query()->with('salesHeader');

        if (request()->filled('start_date') && request()->filled('end_date')) {
            $query->whereBetween('date', [request()->start_date, request()->end_date]);
        }

        return datatables()->of($query)
            ->addColumn('action', function($row){
                $showUrl = route('payment.show', $row->code);
                $editUrl = route('payment.edit', $row->code);
                
                return '
                    <div class="flex items-center justify-start gap-2">
                        <a href="'.$showUrl.'" class="p-1.5 text-gray-400 hover:text-cyan-600 hover:bg-cyan-100 rounded-md transition-all duration-150" title="Detail Payment">
                            <i class="fa-solid fa-eye text-sm"></i>
                        </a>
                        <a href="'.$editUrl.'" class="p-1.5 text-gray-400 hover:text-cyan-600 hover:bg-cyan-100 rounded-md transition-all duration-150" title="Edit Payment">
                            <i class="fa-solid fa-pen text-sm"></i>
                        </a>
                        <button type="button" onclick="deletePayment(\''.$row->code.'\')" class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-all duration-150 cursor-pointer" title="Delete Payment">
                            <i class="fa-solid fa-trash text-sm"></i>
                        </button>
                    </div>';
            })
            ->editColumn('amount', function($row) {
                return 'Rp ' . number_format($row->amount, 2);
            })
            ->editColumn('date', function($row) {
                return \Carbon\Carbon::parse($row->date)->format('d-m-Y');
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        $breadcrumbs = [
            ['label' => 'Payment', 'url' => route('payment.index')],
            ['label' => 'Create Payment', 'url' => null],
        ];

        // Fetch sales that are not fully paid
        $sales = $this->salesRepository->query()->with('payments')->get()->filter(function ($sale) {
            return $sale->status !== 'paid';
        })->values();

        return [
            'breadcrumbs' => $breadcrumbs,
            'code' => $this->codeGenerator->generatePaymentCode(),
            'sales' => $sales,
        ];
    }

    public function store(array $data)
    {
        $data['code'] = $this->codeGenerator->generatePaymentCode();
        return $this->paymentRepository->create($data);
    }

    public function show(string $code)
    {
        $payment = $this->paymentRepository->find($code);
        if (!$payment) return null;

        $breadcrumbs = [
            ['label' => 'Payment', 'url' => route('payment.index')],
            ['label' => 'Details', 'url' => null],
        ];

        return [
            'breadcrumbs' => $breadcrumbs,
            'payment' => $payment,
        ];
    }

    public function edit(string $code)
    {
        $payment = $this->paymentRepository->find($code);
        if (!$payment) return null;

        $breadcrumbs = [
            ['label' => 'Payment', 'url' => route('payment.index')],
            ['label' => 'Edit Payment', 'url' => null],
        ];

        // Fetch sales that are not fully paid OR is the sale currently linked to this payment
        $sales = $this->salesRepository->query()->with('payments')->get()->filter(function ($sale) use ($payment) {
            return $sale->status !== 'paid' || $sale->sales_code === $payment->sales_code;
        })->values();

        return [
            'breadcrumbs' => $breadcrumbs,
            'payment' => $payment,
            'sales' => $sales,
        ];
    }

    public function update(string $code, array $data)
    {
        return $this->paymentRepository->update($code, $data);
    }

    public function delete(string $code)
    {
        return $this->paymentRepository->delete($code);
    }
}
