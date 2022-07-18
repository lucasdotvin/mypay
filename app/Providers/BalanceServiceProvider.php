<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class BalanceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \App\Contracts\Balance\BalanceService::class,
            \App\Services\Balance\LocalBalanceService::class,
        );
    }
}
