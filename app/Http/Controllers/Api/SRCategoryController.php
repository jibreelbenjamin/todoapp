<?php

namespace App\Http\Controllers\Api;

use App\Services\CategoryService;
use Illuminate\Http\Request;

class SRCategoryController
{
    protected $rules = [
        'title' => 'required|string',
        'color' => 'required|string',
    ];

    protected $messages = [];

    public function __construct(
        private CategoryService $categoryService
    ) {}

    public function index()
    {
        $categories = $this->categoryService->getAllCategoriesUser();

        return response()->json([
            'message' => 'OK',
            'data' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules, $this->messages);

        $category = $this->categoryService->createCategory($data);

        return response()->json([
            'message' => 'OK kebuat',
            'data' => $category,
        ], 201);
    }

    public function show($id)
    {
        $category = $this->categoryService->getCategory($id);

        if (! $category) {
            return response()->json(['message' => 'GAK ADA'], 404);
        }

        return response()->json([
            'message' => 'OK ada',
            'data' => $category,
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate($this->rules, $this->messages);

        $updated = $this->categoryService->updateCategory($id, $data);

        if (! $updated) {
            return response()->json(['message' => 'GAK ADA'], 404);
        }

        return response()->json(['message' => 'OK keupdate', 'data' => $data]);
    }

    public function destroy($id)
    {
        $deleted = $this->categoryService->deleteCategory($id);

        if (! $deleted) {
            return response()->json(['message' => 'GAK ADA'], 404);
        }

        return response()->json(['message' => 'OK kehapus']);
    }
}
