<?php

namespace App\Providers;

use App\Models\View as ViewModel;
use Illuminate\Support\Facades\Auth;
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
            $sidebarMenu = Auth::user()
                ->load('mainViews.subViews')
                ->getRelation('mainViews');

            $nowView = ViewModel::where(
                'path',
                request()->getPathInfo()
            )->first();

            $views->with(compact('sidebarMenu', 'nowView'));
        });
    }
}
