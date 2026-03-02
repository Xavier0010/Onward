<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DBMSController extends Controller
{
    protected $models = [
        "users" => \App\Models\User::class,
        // "todos" => \App\Models\Todo::class
    ];

    public function index($table) {
        if (!isset($this->models[$table])) {
            return response()->json([
                "success" => false,
                "message" => "Table not found!"
            ], 404);
        }

        $model = $this->models[$table];
        $data = $model::all();

        return response()->json([
            "success" => true,
            "data" => $data
        ]);
    }

    public function show($table, $id) {
        if(!isset($this->models[$table])) {
            return response()->json([
                "success" => false,
                "message" => "Table not found!"
            ]);
        }

        $model = $this->models[$table];
        $item = $model::find($id);

        if (!$item) {
            return response()->json([
                "success" => false,
                "message" => "Record not found!"
            ]);
        }

        return response()->json([
            "success" => true,
            "data" => $item
        ]);
    }

    public function store(Request $request, $table) {
        if(!isset($this->models[$table])) {
            return response()->json([
                "success" => false,
                "message" => "Table not found!"
            ], 404);
        }

        $model = $this->models[$table];
        $item = $model::create($request->all());

        return response()->json([
            "success" => true,
            "data" => $item,
            "message" => "New record in $model has been created successfully."
        ], 201);
    }

    public function update(Request $request, $table, $id) {
        if(!isset($this->models[$table])) {
            return response()->json([
                "success" => false,
                "message" => "Table not found!"
            ], 404);
        }

        $model = $this->models[$table];
        $item = $model::find($id);

        if (!$item) {
            return response()->json([
                "success" => false,
                "message" => "Record not found!"
            ], 404);
        }

        $item->update($request->all());
        return response()->json([
            "success" => true,
            "message" => "Record with ID $id in $model has been updated successfully!"
        ], 201);
    }

    public function destroy($table, $id) {
        $user = User::find($id);

        if (!isset($this->models[$table])) {
            return response()->json([
                "success" => false,
                "message" => "Table not found!"
            ], 404);
        }

        $model = $this->models[$table];
        $item = $model::find($id);

        if (!$item) {
            return response()->json([
                "success" => false,
                "message" => "Record not found!"
            ], 404);
        }
        
        $item->delete();

        return response()->json([
            "success" => true,
            "message" => "Item with ID $id in $model has been deleted successfully!"
        ]);
    }
}
