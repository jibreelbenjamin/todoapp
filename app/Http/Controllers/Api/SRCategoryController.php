<?php

namespace App\Http\Controllers\Api;

use App\Services\CategoryService;
use Illuminate\Http\Request;

class SRCategoryController
{
    protected $id = 'id';
    protected $table = 'categories';
    protected $idKeyRequest = 'id_category';
    protected $rules = [
        'title' => 'required|string',
        'color' => 'required|string',
    ];

    protected $messages = [];

    public function __construct(
        private CategoryService $service
    ) {}

    public function index()
    {
        $response = $this->service->getAllService();

        return response()->json([
            'message' => 'OK',
            'data' => $response,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules, $this->messages);

        $response = $this->service->createService($data);

        return response()->json([
            'message' => 'OK kebuat',
            'data' => $response,
        ], 201);
    }

    public function show($id)
    {
        $response = $this->service->getService($id);

        if (! $response) {
            return response()->json(['message' => 'GAK ADA'], 404);
        }

        return response()->json([
            'message' => 'OK ada',
            'data' => $response,
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate($this->rules, $this->messages);

        $response = $this->service->updateService($id, $data);

        if (! $response) {
            return response()->json(['message' => 'GAK ADA'], 404);
        }

        return response()->json(['message' => 'OK keupdate', 'data' => $data]);
    }

    public function destroy($id)
    {
        $response = $this->service->deleteService($id);

        if (! $response) {
            return response()->json(['message' => 'GAK ADA'], 404);
        }

        return response()->json(['message' => 'OK kehapus']);
    }

    public function bulkDestroy(Request $request){
        $data = $request->validate([
            "{$this->idKeyRequest}" => "required|array|min:1",
            "{$this->idKeyRequest}.*" => "integer|exists:{$this->table},{$this->id}",
        ]);

        $response = $this->service->bulkDeleteService($data[$this->idKeyRequest]);

        if (! $response) {
            return response()->json(['message' => 'gabisa'], 404);
        }

        return response()->json(['message' => 'OK kehapus semua']);
    }
}
