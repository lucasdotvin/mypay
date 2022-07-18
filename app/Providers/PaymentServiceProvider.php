<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \App\Contracts\Payments\PaymentRepository::class,
            \App\Repositories\Payments\EloquentPaymentRepository::class,
        );

        $this->app->bind(
            \App\Contracts\Payments\PaymentService::class,
            \App\Services\Payments\LocalPaymentService::class,
        );

        $this->app->bind(
            \App\Contracts\Payments\Authorization\AuthorizationService::class,
            \App\Services\Payments\Authorization\MockyAuthorizator\Service::class,
        );

        $this->app->bind(
            \App\Contracts\Payments\Notification\NotificationService::class,
            \App\Services\Payments\Notification\MocklabNotification\Service::class,
        );
    }
}
