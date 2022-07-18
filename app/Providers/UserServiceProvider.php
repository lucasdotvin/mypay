<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \App\Contracts\UserRepository::class,
            \App\Repositories\EloquentUserRepository::class,
        );

        $this->app->bind(
            \App\Contracts\UserService::class,
            \App\Services\UserService::class,
        );
    }
}
