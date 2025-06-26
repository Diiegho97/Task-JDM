<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Task;
use App\Policies\TaskPolicy;

class AppServiceProvider extends ServiceProvider 
{
    protected $policies = [
        Task::class => TaskPolicy::class,
    ];
    /**
    * Register any application services.
    */

    public function register(): void {
        //
    }

    /**
    * Bootstrap any application services.
    */

    public function boot(): void {
        \Carbon\Carbon::setLocale('es');
        date_default_timezone_set('America/Bogota');
    }
}
