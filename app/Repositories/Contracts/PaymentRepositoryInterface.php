<?php

namespace App\Repositories\Contracts;

use App\Models\Payment;

interface PaymentRepositoryInterface
{
    public function query();
    public function find(string $code): ?Payment;
    public function create(array $data): Payment;
    public function update(string $code, array $data): Payment;
    public function delete(string $code): bool;
}
