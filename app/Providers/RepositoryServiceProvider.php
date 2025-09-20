<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // $repositories = [
        //     \App\Repositories\Contracts\CustomerRepositoryInterface::class => \App\Repositories\Eloquent\CustomerRepository::class,
        //     \App\Repositories\Contracts\DriverRepositoryInterface::class   => \App\Repositories\Eloquent\DriverRepository::class,
        //     \App\Repositories\Contracts\CompanyRepositoryInterface::class  => \App\Repositories\Eloquent\CompanyRepository::class,
        //     \App\Repositories\Contracts\OrderRepositoryInterface::class    => \App\Repositories\Eloquent\OrderRepository::class,
        // ];

        // foreach ($repositories as $interface => $implementation) {
        //     $this->app->bind($interface, $implementation);
        // }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
