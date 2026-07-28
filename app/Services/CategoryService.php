<?php

namespace App\Services;

use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getAllCategoriesUser(): Collection
    {
        return $this->categoryRepository->allByUser();
    }

    public function getCategory(int $id): ?object
    {
        $category = $this->categoryRepository->findById($id);
        if ($category && $category['id_user'] != auth()->id()) {
            return null;
        }

        return $category;
    }

    public function createCategory(array $data): object
    {
        $data['id_user'] = auth()->id();

        return $this->categoryRepository->create($data);
    }

    public function updateCategory(int $id, array $data): bool
    {
        $category = $this->categoryRepository->findById($id);
        if ($category && $category['id_user'] != auth()->id()) {
            return false;
        }

        return $this->categoryRepository->update($id, $data);
    }

    public function deleteCategory(int $id): bool
    {
        $category = $this->categoryRepository->findById($id);
        if ($category && $category['id_user'] != auth()->id()) {
            return false;
        }

        return $this->categoryRepository->delete($id);
    }
}
