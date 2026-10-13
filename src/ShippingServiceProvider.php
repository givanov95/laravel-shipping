<?php

declare(strict_types=1);

namespace Givanov95\Shipping;

use Illuminate\Support\ServiceProvider;

final class ShippingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // One registry for the application: every package adds its method to the same one.
        $this->app->singleton(DeliveryMethods::class);
    }
}
