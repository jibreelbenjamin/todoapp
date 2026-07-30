<?php

namespace App\Services;

use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(
        private CategoryRepositoryInterface $repository
    ) {}

    public function getAllService(): Collection
    {
        return $this->repository->allByUser();
    }

    public function getService(int $id): ?object
    {
        $collection = $this->repository->findById($id);
        if ($collection && $collection['id_user'] != auth()->id()) {
            return null;
        }

        return $collection;
    }

    public function createService(array $data): object
    {
        $data['id_user'] = auth()->id();

        return $this->repository->create($data);
    }

    public function updateService(int $id, array $data): bool
    {
        $collection = $this->repository->findById($id);
        if ($collection && $collection['id_user'] != auth()->id()) {
            return false;
        }

        return $this->repository->update($id, $data);
    }

    public function deleteService(int $id): bool
    {
        $collection = $this->repository->findById($id);
        if ($collection && $collection['id_user'] != auth()->id()) {
            return false;
        }

        return $this->repository->delete($id);
    }

    public function bulkDeleteService(array $data)
    {
        $owned = $this->repository->checkOwner($data);

        return $this->repository->bulkDelete($owned);
    }
}
