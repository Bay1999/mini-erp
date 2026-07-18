<?php

namespace App\Repositories\Contracts;

use App\Models\Sales\SalesHeader;

interface SalesRepositoryInterface
{
    public function query();
    public function find(string $code): ?SalesHeader;
    public function create(array $data): SalesHeader;
    public function update(string $code, array $data): SalesHeader;
    public function delete(string $code): bool;
}
