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
}
