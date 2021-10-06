<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.navbars.sidebar', function ($views) {

            $allMenu = DB::table('role_assignment')
                ->join('view_permission', 'role_assignment.role_id', '=', 'view_permission.role_id')
                ->join('views', 'view_permission.view_id', '=', 'views.id')
                ->where('role_assignment.user_id', '=', Auth::id())
                ->where('views.active', '=', 1)
                ->select('views.module', 'views.level', 'views.order', 'views.menu_title', 'views.menu_slug', 'views.path')
                ->orderByRaw('views.module , views.level , views.order')
                ->get();

            list($p, $c) = $allMenu->partition(function ($i) {
                return $i->level === 1;
            });

            $parents = $p;

            $child = $c;

            $views->with('parentMenu', $parents)
                  ->with('childMenu', $child);
        });
    }
}
