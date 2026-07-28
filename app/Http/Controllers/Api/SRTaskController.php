<?php

namespace App\Http\Controllers\Api;

use App\Services\TaskService;
use Illuminate\Http\Request;

class SRTaskController
{
    protected $rules = [
        'id_category' => 'required|exists:categories,id',
        'title' => 'required|string',
        'description' => 'string',
        'status' => 'enum:drop,pending,progress,done',
        'priority' => 'enum:none,low,medium,high',
        'due_date' => 'date',
    ];
    protected $messages = [

    ];

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
}
