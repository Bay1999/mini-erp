<?php

namespace App\Http\Controllers;

use App\Services\ItemService;
use Illuminate\Http\Request;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\ErrorResource;
use Illuminate\Validation\ValidationException;
use Exception;

class ItemController extends Controller
{
    protected ItemService $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->itemService->index();
        return view("pages.item.item-index", $data);
    }

    public function data()
    {
        return $this->itemService->indexDataTable();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $this->itemService->create();
        return view("pages.item.item-form", $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            $imageFile = $request->file('image');
            $item = $this->itemService->store($validated, $imageFile);

            return new SuccessResource($item, 'Item created successfully.');
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
        $data = $this->itemService->show($code);
        if (!$data) {
            return redirect()->route('master.item.index')->with('error', 'Item not found.');
        }
        return view("pages.item.item-show", $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $code)
    {
        try {
            $data = $this->itemService->edit($code);
            if (!$data) {
                return redirect()->route('master.item.index')->with('error', 'Item not found.');
            }
            return view("pages.item.item-form", $data);
        } catch (Exception $e) {
            return redirect()->route('master.item.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $code)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            $imageFile = $request->file('image');
            $this->itemService->update($code, $validated, $imageFile);

            return new SuccessResource(null, 'Item updated successfully.');
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
            $this->itemService->delete($code);
            return new SuccessResource(null, 'Item deleted successfully.');
        } catch (Exception $e) {
            return new ErrorResource(null, $e->getMessage(), 422);
        }
    }
}
