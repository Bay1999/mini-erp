<?php

namespace App\Repositories\Eloquent;

use App\Models\Item;
use App\Repositories\Contracts\ItemRepositoryInterface;

class ItemRepository implements ItemRepositoryInterface
{
    public function query()
    {
        return Item::query();
    }

    public function find(string $code): ?Item
    {
        return Item::where('code', $code)->first();
    }

    public function create(array $data): Item
    {
        return Item::create($data);
    }

    public function update(string $code, array $data): bool
    {
        $item = $this->find($code);
        if ($item) {
            return $item->update($data);
        }
        return false;
    }

    public function delete(string $code): bool
    {
        $item = $this->find($code);
        if ($item) {
            return $item->delete();
        }
        return false;
    }
}
