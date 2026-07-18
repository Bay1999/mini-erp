<?php

namespace App\Repositories\Contracts;

use App\Models\Item;

interface ItemRepositoryInterface
{
    public function query();
    public function find(string $code): ?Item;
    public function create(array $data): Item;
    public function update(string $code, array $data): bool;
    public function delete(string $code): bool;
}
