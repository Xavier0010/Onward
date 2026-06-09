<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AdminDbms extends Component
{
    use WithPagination;

    public $tables = [];
    public $activeTable = '';
    
    public $search = '';
    public $sortBy = 'id';
    public $sortDir = 'desc';
    
    public $showModal = false;
    public $showDeleteModal = false;
    public $showDetailsModal = false;
    
    public $editId = null;
    public $formData = [];
    public $columns = [];
    public $columnDetails = [];
    public $foreignKeys = [];
    public $selectedRecord = null;

    public function mount()
    {
        $this->tables = $this->getTables();
        
        if (count($this->tables) > 0) {
            $this->selectTable($this->tables[0]);
        }
    }
    
    private function getTables()
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
            return !in_array($table, [
                'migrations', 'failed_jobs', 'personal_access_tokens', 'password_reset_tokens',
                'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches'
            ]);
        });
        
        return array_values($tablesList);
    }
    
    public function selectTable($table)
    {
        $this->activeTable = $table;
        $this->reset(['search', 'editId', 'formData', 'showModal', 'showDeleteModal', 'showDetailsModal']);
        $this->resetPage();
        $this->render();
        
        $this->columns = Schema::getColumnListing($this->activeTable);
        $this->columnDetails = $this->getColumnDetails($this->activeTable);
        $this->foreignKeys = $this->getForeignKeys($this->activeTable);
        
        if (in_array('id', $this->columns)) {
            $this->sortBy = 'id';
            $this->sortDir = 'desc';
        } else {
            $this->sortBy = $this->columns[0] ?? '';
            $this->sortDir = 'asc';
        }
    }
    
    private function getColumnDetails($table)
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $details = [];
        
        if ($driver === 'mysql') {
            $dbName = $connection->getDatabaseName();
            $columns = DB::select("
                SELECT 
                    COLUMN_NAME as name,
                    DATA_TYPE as type,
                    IS_NULLABLE as nullable,
                    COLUMN_DEFAULT as default_val,
                    COLUMN_TYPE as full_type
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
            ", [$dbName, $table]);
            
            foreach ($columns as $col) {
                $details[$col->name] = [
                    'type' => $col->type,
                    'nullable' => $col->nullable === 'YES',
                    'default' => $col->default_val,
                    'full_type' => $col->full_type,
                ];
                
                // Check for enum
                if (str_starts_with($col->full_type, 'enum')) {
                    preg_match("/^enum\('(.*)'\)$/", $col->full_type, $matches);
                    if (isset($matches[1])) {
                        $details[$col->name]['enum_values'] = explode("','", $matches[1]);
                    }
                }
            }
        } elseif ($driver === 'sqlite') {
            $columns = DB::select("PRAGMA table_info($table)");
            foreach ($columns as $col) {
                $type = strtolower($col->type);
                $details[$col->name] = [
                    'type' => $type,
                    'nullable' => !$col->notnull,
                    'default' => $col->dflt_value,
                    'full_type' => $col->type,
                ];
            }
        }
        
        return $details;
    }
    
    private function getForeignKeys($table)
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $keys = [];
        
        if ($driver === 'mysql') {
            $dbName = $connection->getDatabaseName();
            $fks = DB::select("
                SELECT 
                    COLUMN_NAME as column_name,
                    REFERENCED_TABLE_NAME as foreign_table,
                    REFERENCED_COLUMN_NAME as foreign_column
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = ? 
                  AND TABLE_NAME = ? 
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$dbName, $table]);
            
            foreach ($fks as $fk) {
                $keys[$fk->column_name] = [
                    'table' => $fk->foreign_table,
                    'column' => $fk->foreign_column,
                    'options' => DB::table($fk->foreign_table)->pluck($fk->foreign_column, 'id')->toArray(),
                ];
            }
        } elseif ($driver === 'sqlite') {
            $fks = DB::select("PRAGMA foreign_key_list($table)");
            foreach ($fks as $fk) {
                $keys[$fk->from] = [
                    'table' => $fk->table,
                    'column' => $fk->to,
                    'options' => DB::table($fk->table)->pluck($fk->to, 'id')->toArray(),
                ];
            }
        }
        
        return $keys;
    }
    
    public function updatingSearch() { $this->resetPage(); }
    
    public function sortByCol($col)
    {
        if ($this->sortBy === $col) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $col;
            $this->sortDir = 'asc';
        }
    }
    
    public function openCreateModal()
    {
        $this->editId = null;
        $this->formData = [];
        $this->showModal = true;
    }

    public function editRecord($id)
    {
        $this->editId = $id;
        $record = (array) DB::table($this->activeTable)->where('id', $id)->first();
        
        // Format datetime fields for datetime-local input
        foreach ($record as $col => $value) {
            if ($value && isset($this->columnDetails[$col])) {
                $type = $this->columnDetails[$col]['type'] ?? '';
                if (in_array($type, ['datetime', 'timestamp'])) {
                    try {
                        $record[$col] = Carbon::parse($value)->format('Y-m-d\TH:i');
                    } catch (\Exception $e) {
                        // If parsing fails, leave as is
                    }
                }
            }
        }
        
        $this->formData = $record;
        $this->showModal = true;
    }
    
    public function showDetails($id)
    {
        $this->selectedRecord = DB::table($this->activeTable)->where('id', $id)->first();
        $this->showDetailsModal = true;
    }
    
    public function confirmDelete($id)
    {
        $this->editId = $id;
        $this->showDeleteModal = true;
    }
    
    public function deleteRecord()
    {
        DB::table($this->activeTable)->where('id', $this->editId)->delete();
        $this->showDeleteModal = false;
        $this->editId = null;
    }
    
    public function saveRecord()
    {
        $data = [];
        foreach ($this->columns as $col) {
            if ($col !== 'id' && $col !== 'created_at' && $col !== 'updated_at' && array_key_exists($col, $this->formData)) {
                $value = $this->formData[$col];
                
                // Handle empty strings for nullable fields
                if ($value === '' && isset($this->columnDetails[$col]) && $this->columnDetails[$col]['nullable']) {
                    $data[$col] = null;
                } else {
                    $data[$col] = $value;
                }
            }
        }
        
        if (in_array('updated_at', $this->columns)) $data['updated_at'] = now();
        
        if ($this->editId) {
            DB::table($this->activeTable)->where('id', $this->editId)->update($data);
        } else {
            if (in_array('created_at', $this->columns)) $data['created_at'] = now();
            DB::table($this->activeTable)->insert($data);
        }
        
        $this->showModal = false;
    }
    
    public function render()
    {
        if (!$this->activeTable) {
            return view('livewire.admin_dbms', ['records' => collect()]);
        }

        $query = DB::table($this->activeTable);

        if ($this->search != '') {
            $query->where(function ($q) {
                foreach ($this->columns as $col) {
                    $q->orWhere($col, 'like', '%' . $this->search . '%');
                }
            });
        }

        if ($this->sortBy) {
            $query->orderBy($this->sortBy, $this->sortDir);
        }

        $records = $query->paginate(10);
        
        return view('livewire.admin_dbms', [
            'records' => $records
        ]);
    }
}