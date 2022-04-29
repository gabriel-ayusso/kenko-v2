<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
        Blade::include('components.textbox', 'textbox');
        Blade::include('components.textarea', 'textarea');
        Blade::include('components.checkbox', 'checkbox');
        Blade::include('components.radio', 'radio');
        Blade::include('components.hidden', 'hidden');

        Paginator::useBootstrapFive();
    }
}
