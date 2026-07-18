<?php

namespace App\Services;

use App\Repositories\Contracts\ItemRepositoryInterface;
use App\Services\CodeGeneratorService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemService
{
    protected ItemRepositoryInterface $itemRepository;
    protected CodeGeneratorService $codeGenerator;

    public function __construct(ItemRepositoryInterface $itemRepository, CodeGeneratorService $codeGenerator)
    {
        $this->itemRepository = $itemRepository;
        $this->codeGenerator = $codeGenerator;
    }

    public function index()
    {
        $breadcrumbs = [
            ['label' => 'Master', 'url' => null],
            ['label' => 'Item', 'url' => route('master.item.index')],
        ];

        return [
            'breadcrumbs' => $breadcrumbs,
        ];
    }

    public function indexDataTable()
    {
        $query = $this->itemRepository->query()->withCount('salesDetails');

        return datatables()->of($query)
            ->addColumn('action', function($row){
                $showUrl = route('master.item.show', $row->code);
                $editUrl = route('master.item.edit', $row->code);
                $isUsed = $row->sales_details_count > 0;
                
                $editBtn = $isUsed 
                    ? '<span class="p-1.5 text-gray-300 cursor-not-allowed rounded-md inline-block" title="Cannot edit item: already used in sales">
                           <i class="fa-solid fa-pen text-sm"></i>
                       </span>'
                    : '<a href="'.$editUrl.'" class="p-1.5 text-gray-400 hover:text-cyan-600 hover:bg-cyan-100 rounded-md transition-all duration-150" title="Edit Item">
                           <i class="fa-solid fa-pen text-sm"></i>
                       </a>';
                       
                $deleteBtn = $isUsed
                    ? '<button type="button" disabled class="p-1.5 text-gray-300 cursor-not-allowed rounded-md" title="Cannot delete item: already used in sales">
                           <i class="fa-solid fa-trash text-sm"></i>
                       </button>'
                    : '<button type="button" onclick="deleteItem(\''.$row->code.'\', \''.e($row->name).'\')" class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-all duration-150 cursor-pointer" title="Delete Item">
                           <i class="fa-solid fa-trash text-sm"></i>
                       </button>';
                
                return '
                    <div class="flex items-center justify-start gap-2">
                        <a href="'.$showUrl.'" class="p-1.5 text-gray-400 hover:text-cyan-600 hover:bg-cyan-100 rounded-md transition-all duration-150" title="Detail Item">
                            <i class="fa-solid fa-eye text-sm"></i>
                        </a>
                        ' . $editBtn . '
                        ' . $deleteBtn . '
                    </div>';
            })
            ->editColumn('image', function($row) {
                $url = $row->image ? asset('storage/' . $row->image) : 'https://images.unsplash.com/photo-1546502208-81d149d52bd7?w=100&auto=format&fit=crop&q=60';
                return '<img src="'.$url.'" class="h-10 w-10 object-cover rounded-lg border border-gray-150 shadow-sm">';
            })
            ->editColumn('price', function($row) {
                return 'Rp ' . number_format($row->price, 2);
            })
            ->rawColumns(['action', 'image'])
            ->make(true);
    }

    public function create()
    {
        $breadcrumbs = [
            ['label' => 'Master', 'url' => null],
            ['label' => 'Item', 'url' => route('master.item.index')],
            ['label' => 'Create Item', 'url' => null],
        ];

        return [
            'breadcrumbs' => $breadcrumbs,
            'code' => $this->codeGenerator->generateItemCode(),
        ];
    }

    public function store(array $data, ?UploadedFile $imageFile)
    {
        $data['code'] = $this->codeGenerator->generateItemCode();

        if ($imageFile) {
            $data['image'] = $imageFile->store('items', 'public');
        } else {
            $data['image'] = '';
        }

        return $this->itemRepository->create($data);
    }

    public function edit(string $code)
    {
        $item = $this->itemRepository->find($code);
        if (!$item) {
            return null;
        }

        if ($item->salesDetails()->count() > 0) {
            throw new \Exception("Cannot edit item: already used in sales.");
        }

        $breadcrumbs = [
            ['label' => 'Master', 'url' => null],
            ['label' => 'Item', 'url' => route('master.item.index')],
            ['label' => 'Edit Item', 'url' => null],
        ];

        return [
            'breadcrumbs' => $breadcrumbs,
            'item' => $item,
        ];
    }

    public function show(string $code)
    {
        $item = $this->itemRepository->find($code);
        if (!$item) {
            return null;
        }

        $breadcrumbs = [
            ['label' => 'Master', 'url' => null],
            ['label' => 'Item', 'url' => route('master.item.index')],
            ['label' => 'Detail Item', 'url' => null],
        ];

        return [
            'breadcrumbs' => $breadcrumbs,
            'item' => $item,
        ];
    }

    public function update(string $code, array $data, ?UploadedFile $imageFile)
    {
        $item = $this->itemRepository->find($code);
        if (!$item) {
            return false;
        }

        if ($item->salesDetails()->count() > 0) {
            throw new \Exception("Cannot update item: already used in sales.");
        }

        if ($imageFile) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $data['image'] = $imageFile->store('items', 'public');
        }

        return $this->itemRepository->update($code, $data);
    }

    public function delete(string $code): bool
    {
        $item = $this->itemRepository->find($code);
        if (!$item) {
            return false;
        }

        if ($item->salesDetails()->count() > 0) {
            throw new \Exception("Cannot delete item: already used in sales.");
        }

        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }

        return $this->itemRepository->delete($code);
    }
}
