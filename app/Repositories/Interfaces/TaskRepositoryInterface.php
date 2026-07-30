<?php

namespace App\Repositories\Interfaces;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    public function allByUser(): Collection;

    public function findById(int $id): ?Task;

    public function create(array $data): Task;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;

    public function checkOwner(array $data);

    public function bulkDelete(array $data): bool;

    public function bulkStatus(array $data, string $status): bool;
}
