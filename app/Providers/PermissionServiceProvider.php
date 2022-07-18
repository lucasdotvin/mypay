<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \App\Contracts\Permissions\PermissionsService::class,
            \App\Services\Permissions\LocalPermissionsService::class,
        );
    }
}
