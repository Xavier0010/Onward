<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Models\Todo;
use App\Models\Achievement;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DBMSController extends Controller
{
    public function tables()
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        
        if ($driver === 'sqlite') {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            $tablesList = array_map(function($table) {
                return $table->name;
            }, $tables);
        } else {
            $tables = DB::select('SHOW TABLES');
            $dbName = 'Tables_in_' . env('DB_DATABASE', $connection->getDatabaseName());
            $tablesList = array_map(function($table) use ($dbName) {
                return $table->$dbName ?? array_values((array)$table)[0];
            }, $tables);
        }
        
        $tablesList = array_filter($tablesList, function($table) {
            return !in_array($table, ['migrations', 'failed_jobs', 'personal_access_tokens', 'password_reset_tokens']);
        });
        
        return response()->json([
            'success' => true,
            'data' => array_values($tablesList)
        ]);
    }

    public function tableInfo($table)
    {
        if (!Schema::hasTable($table)) {
            return response()->json([
                'success' => false,
                'message' => 'Table not found!'
            ], 404);
        }

        $columns = Schema::getColumnListing($table);
        
        $filterOptions = [];
        foreach ($columns as $col) {
            if (in_array($col, ['id', 'created_at', 'updated_at', 'description', 'task', 'password', 'avatar', 'first_name', 'last_name', 'email', 'username', 'email_verified_at', 'remember_token'])) continue;
            
            try {
                $distinctCount = DB::table($table)->distinct($col)->count($col);
                if ($distinctCount > 0 && $distinctCount <= 15) {
                    $filterOptions[$col] = DB::table($table)->whereNotNull($col)->distinct()->pluck($col)->toArray();
                }
            } catch (\Exception $e) {}
        }

        return response()->json([
            'success' => true,
            'data' => [
                'columns' => $columns,
                'filterOptions' => $filterOptions
            ]
        ]);
    }

    public function index(Request $request, $table) {
        if (!Schema::hasTable($table)) {
            return response()->json([
                "success" => false,
                "message" => "Table not found!"
            ], 404);
        }

        $query = DB::table($table);

        if ($request->has('search') && $request->search != '') {
            $columns = Schema::getColumnListing($table);
            $query->where(function ($q) use ($columns, $request) {
                foreach ($columns as $col) {
                    $q->orWhere($col, 'like', '%' . $request->search . '%');
                }
            });
        }

        if ($request->has('filters')) {
            foreach ($request->filters as $col => $val) {
                if ($val !== '') {
                    $query->where($col, $val);
                }
            }
        }

        if ($request->has('sortBy')) {
            $query->orderBy($request->sortBy, $request->sortDir ?? 'asc');
        }

        $perPage = $request->get('perPage', 10);
        $data = $query->paginate($perPage);

        return response()->json([
            "success" => true,
            "data" => $data
        ]);
    }

    public function show($table, $id) {
        if (!Schema::hasTable($table)) {
            return response()->json([
                "success" => false,
                "message" => "Table not found!"
            ], 404);
        }

        $item = DB::table($table)->where('id', $id)->first();

        if (!$item) {
            return response()->json([
                "success" => false,
                "message" => "Record not found!"
            ], 404);
        }

        return response()->json([
            "success" => true,
            "data" => $item
        ]);
    }

    public function store(Request $request, $table) {
        if (!Schema::hasTable($table)) {
            return response()->json([
                "success" => false,
                "message" => "Table not found!"
            ], 404);
        }

        $data = $request->all();
        $columns = Schema::getColumnListing($table);
        if (in_array('created_at', $columns)) $data['created_at'] = now();
        if (in_array('updated_at', $columns)) $data['updated_at'] = now();

        DB::table($table)->insert($data);

        return response()->json([
            "success" => true,
            "message" => "New record in $table has been created successfully."
        ], 201);
    }

    public function update(Request $request, $table, $id) {
        if (!Schema::hasTable($table)) {
            return response()->json([
                "success" => false,
                "message" => "Table not found!"
            ], 404);
        }

        $item = DB::table($table)->where('id', $id)->first();

        if (!$item) {
            return response()->json([
                "success" => false,
                "message" => "Record not found!"
            ], 404);
        }

        $data = $request->all();
        $columns = Schema::getColumnListing($table);
        if (in_array('updated_at', $columns)) $data['updated_at'] = now();

        DB::table($table)->where('id', $id)->update($data);

        return response()->json([
            "success" => true,
            "message" => "Record with ID $id in $table has been updated successfully!"
        ], 200);
    }

    public function destroy($table, $id) {
        if (!Schema::hasTable($table)) {
            return response()->json([
                "success" => false,
                "message" => "Table not found!"
            ], 404);
        }

        $item = DB::table($table)->where('id', $id)->first();

        if (!$item) {
            return response()->json([
                "success" => false,
                "message" => "Record not found!"
            ], 404);
        }
        
        DB::table($table)->where('id', $id)->delete();

        return response()->json([
            "success" => true,
            "message" => "Item with ID $id in $table has been deleted successfully!"
        ]);
    }
}
