<?php

namespace ZaLaravel\LaravelPayeer;

use Illuminate\Support\ServiceProvider;

/**
 * Class LaravelPayeerServiceProvider
 * @package ZaLaravel\LaravelPayeer
 */
class LaravelPayeerServiceProvider extends ServiceProvider{

    /**
     * @return void
     */
    public function boot()
    {
        // Routes
        include __DIR__.'/../../routes.php';
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // TODO: Implement register() method.
    }
}