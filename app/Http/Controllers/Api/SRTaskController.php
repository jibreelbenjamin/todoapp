<?php

namespace App\Http\Controllers\Api;

use App\Services\TaskService;
use Illuminate\Http\Request;

class SRTaskController
{
    protected $id = 'id';

    protected $table = 'tasks';

    protected $idKeyRequest = 'id_task';

    protected $rules = [
        'id_category' => 'required|exists:categories,id',
        'title' => 'required|string',
        'description' => 'string',
        'status' => 'enum:drop,pending,progress,done',
        'priority' => 'enum:none,low,medium,high',
        'due_date' => 'date',
    ];

    protected $messages = [];

    public function __construct(
        private TaskService $service
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

    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            "{$this->idKeyRequest}" => 'required|array|min:1',
            "{$this->idKeyRequest}.*" => "integer|exists:{$this->table},{$this->id}",
        ]);

        $response = $this->service->bulkDeleteService($data[$this->idKeyRequest]);

        if (! $response) {
            return response()->json(['message' => 'gabisa'], 404);
        }

        return response()->json(['message' => 'OK kehapus semua']);
    }

    public function bulkStatus(Request $request)
    {
        $data = $request->validate([
            "{$this->idKeyRequest}" => 'required|array|min:1',
            "{$this->idKeyRequest}.*" => "integer|exists:{$this->table},{$this->id}",
            'status' => 'required|in:drop,pending,progress,done',
        ]);

        $response = $this->service->bulkStatusService($data);

        if (! $response) {
            return response()->json(['message' => 'gbisa'], 404);
        }

        return response()->json(['message' => 'OK keupdate semua']);
    }

    public function syncTaskGCalendar()
    {
        $response = $this->service->syncAllGCalendar();

        return response()->json([
            'message' => 'OK',
            'data' => $response,
        ]);
    }
}
