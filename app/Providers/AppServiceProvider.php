<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $tables = DB::select("
                SELECT name from sqlite_master
                where type='table'
                AND name NOT LIKE 'sqlite_%'
            ");

            $tables = array_map(fn($t) => $t->name, $tables);
            $segments = request()->segments();
            $currentTable = $segments[1] ?? null; // /admin/{table}
            $view->with([
                'tables' => $tables,
                'currentTable' => $currentTable
            ]);
        });
    }
}
