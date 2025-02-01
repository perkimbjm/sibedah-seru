<?php

namespace App\Providers;

use App\Models\Village;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('components.sidebar-edit', function ($view) {
            $villages = Village::select('id', 'name')
                ->orderBy('name')
                ->get();

            $view->with('villages', $villages);
        });
    }

    public function register()
    {
        //
    }
}
