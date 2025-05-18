<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\Sanctum;

class CashierCustomServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        Sanctum::ignoreMigrations();

        // Cashierのマイグレーション無効
        Cashier::ignoreMigrations();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Log::info('CashierCustomServiceProvider booted.');
        // Cashierのマイグレーションを読み込まない
        Cashier::ignoreMigrations();
    }
}
