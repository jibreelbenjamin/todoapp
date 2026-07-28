<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController
{
    protected $model = Task::class;

    protected $table_primary = 'id';

    protected $data_title = 'kelas';

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

    public function index()
    {
        if ($this->model::all()) {
            $data = $this->model::with('category')->get();

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

    public function store(Request $request)
    {
        $data = $request->validate($this->rules, $this->messages);

        if ($data = $this->model::create($data)) {
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

    public function show($id)
    {
        if ($this->model::find($id)) {
            $data = $this->model::with('category')->find($id);

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

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'id_category' => 'required|exists:categories,id',
            'title' => 'required|string',
            'description' => 'string',
            'status' => 'enum:drop,pending,progress,done',
            'priority' => 'enum:none,low,medium,high',
            'due_date' => 'date',
        ]);

        if ($this->model::find($id)->update($data)) {
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

    public function destroy($id)
    {
        if ($this->model::find($id)) {
            $this->model::find($id)->delete();

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
