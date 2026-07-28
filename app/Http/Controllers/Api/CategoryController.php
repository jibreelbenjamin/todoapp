<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController
{
    public function index(Category $model)
    {
        if ($model::all()) {
            $data = $model::all();

            return response()->json([
                'message' => 'OK',
                'data' => $data,
            ]);
        } else {
            return response()->json([
                'message' => 'GAK ADA',
            ], 404);
        }
    }

    public function store(Request $request, Category $model)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'color' => 'required|string',
        ]);

        if ($data = $model::create($data)) {
            return response()->json([
                'message' => 'OK kebuat',
                'data' => $data,
            ]);
        } else {
            return response()->json([
                'message' => 'GAK OK',
            ], 500);
        }

        return response()->json([
            'message' => 'OK kebuat',
            'data' => $data,
        ], 201);
    }

    public function show(Category $model, $id)
    {
        if ($model::find($id)) {
            $data = $model::find($id);

            return response()->json([
                'message' => 'OK ada',
                'data' => $data,
            ]);
        } else {
            return response()->json([
                'message' => 'GAK ADA',
            ], 404);
        }
    }

    public function update(Request $request, Category $model, $id)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'color' => 'required|string',
        ]);

        if ($model::find($id)->update($data)) {
            return response()->json([
                'message' => 'OK keupdate',
                'data' => $data,
            ]);
        } else {
            return response()->json([
                'message' => 'GAK ADA',
            ], 404);
        }

    }

    public function destroy(Category $model, $id)
    {
        if ($model::find($id)) {
            $model::find($id)->delete();

            return response()->json([
                'message' => 'OK kehapus',
            ]);
        } else {
            return response()->json([
                'message' => 'GAK ADA',
            ], 404);
        }
    }
}
