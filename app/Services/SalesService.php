<?php

namespace App\Services;

use App\Repositories\Contracts\SalesRepositoryInterface;
use App\Repositories\Contracts\ItemRepositoryInterface;
use App\Services\CodeGeneratorService;

class SalesService
{
    protected SalesRepositoryInterface $salesRepository;
    protected CodeGeneratorService $codeGenerator;
    protected ItemRepositoryInterface $itemRepository;

    public function __construct(
        SalesRepositoryInterface $salesRepository,
        CodeGeneratorService $codeGenerator,
        ItemRepositoryInterface $itemRepository
    ) {
        $this->salesRepository = $salesRepository;
        $this->codeGenerator = $codeGenerator;
        $this->itemRepository = $itemRepository;
    }

    public function index()
    {
        $breadcrumbs = [
            ['label' => 'Sales', 'url' => route('sales.index')],
        ];

        return [
            'breadcrumbs' => $breadcrumbs,
        ];
    }

    public function indexDataTable()
    {
        $query = $this->salesRepository->query()->with('payments');

        if (request()->filled('start_date') && request()->filled('end_date')) {
            $query->whereBetween('date', [request()->start_date, request()->end_date]);
        }

        return datatables()->of($query)
            ->addColumn('action', function($row){
                $showUrl = route('sales.show', $row->sales_code);
                $editUrl = route('sales.edit', $row->sales_code);
                $hasPayments = $row->payments->count() > 0;
                
                $editBtn = $hasPayments
                    ? '<span class="p-1.5 text-gray-300 cursor-not-allowed rounded-md inline-block" title="Cannot edit sales: payments already exist">
                           <i class="fa-solid fa-pen text-sm"></i>
                       </span>'
                    : '<a href="'.$editUrl.'" class="p-1.5 text-gray-400 hover:text-cyan-600 hover:bg-cyan-100 rounded-md transition-all duration-150" title="Edit Sales">
                           <i class="fa-solid fa-pen text-sm"></i>
                       </a>';
                       
                $deleteBtn = $hasPayments
                    ? '<button type="button" disabled class="p-1.5 text-gray-300 cursor-not-allowed rounded-md" title="Cannot delete sales: payments already exist">
                           <i class="fa-solid fa-trash text-sm"></i>
                       </button>'
                    : '<button type="button" onclick="deleteSales(\''.$row->sales_code.'\')" class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-all duration-150 cursor-pointer" title="Delete Sales">
                           <i class="fa-solid fa-trash text-sm"></i>
                       </button>';
                
                return '
                    <div class="flex items-center justify-start gap-2">
                        <a href="'.$showUrl.'" class="p-1.5 text-gray-400 hover:text-cyan-600 hover:bg-cyan-100 rounded-md transition-all duration-150" title="Detail Sales">
                            <i class="fa-solid fa-eye text-sm"></i>
                        </a>
                        ' . $editBtn . '
                        ' . $deleteBtn . '
                    </div>';
            })
            ->editColumn('grand_total', function($row) {
                return 'Rp ' . number_format($row->grand_total, 2);
            })
            ->editColumn('date', function($row) {
                return \Carbon\Carbon::parse($row->date)->format('d-m-Y');
            })
            ->editColumn('status', function($row) {
                $statusColors = [
                    'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'unpaid' => 'bg-rose-50 text-rose-700 border-rose-200',
                    'partially_paid' => 'bg-amber-50 text-amber-700 border-amber-200',
                ];
                $colorClass = $statusColors[$row->status] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                return '<span class="px-2 py-1 rounded-full border text-xs font-semibold ' . $colorClass . '">' . ucfirst(str_replace('_', ' ', $row->status)) . '</span>';
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function create()
    {
        $breadcrumbs = [
            ['label' => 'Sales', 'url' => route('sales.index')],
            ['label' => 'Create Sales', 'url' => null],
        ];

        return [
            'breadcrumbs' => $breadcrumbs,
            'code' => $this->codeGenerator->generateSalesCode(),
            'items' => $this->itemRepository->query()->orderBy('name')->get(),
        ];
    }

    public function store(array $data)
    {
        $data['sales_code'] = $this->codeGenerator->generateSalesCode();
        return $this->salesRepository->create($data);
    }

    public function show(string $code)
    {
        $sales = $this->salesRepository->find($code);
        if (!$sales) return null;

        $breadcrumbs = [
            ['label' => 'Sales', 'url' => route('sales.index')],
            ['label' => 'Details', 'url' => null],
        ];

        return [
            'breadcrumbs' => $breadcrumbs,
            'sales' => $sales,
        ];
    }

    public function edit(string $code)
    {
        $sales = $this->salesRepository->find($code);
        if (!$sales) return null;

        if ($sales->payments()->count() > 0) {
            throw new \Exception("Cannot edit sales: payments already exist.");
        }

        $breadcrumbs = [
            ['label' => 'Sales', 'url' => route('sales.index')],
            ['label' => 'Edit Sales', 'url' => null],
        ];

        return [
            'breadcrumbs' => $breadcrumbs,
            'sales' => $sales,
            'items' => $this->itemRepository->query()->orderBy('name')->get(),
        ];
    }

    public function update(string $code, array $data)
    {
        $sales = $this->salesRepository->find($code);
        if ($sales && $sales->payments()->count() > 0) {
            throw new \Exception("Cannot update sales: payments already exist.");
        }
        return $this->salesRepository->update($code, $data);
    }

    public function delete(string $code)
    {
        $sales = $this->salesRepository->find($code);
        if ($sales && $sales->payments()->count() > 0) {
            throw new \Exception("Cannot delete sales: payments already exist.");
        }
        return $this->salesRepository->delete($code);
    }
}
