<?php namespace DreamFactory\Enterprise\Dashboard\Providers;

use DreamFactory\Enterprise\Dashboard\Auth\DashboardUserProvider;
use Illuminate\Support\ServiceProvider;

class DashboardAuthProvider extends ServiceProvider
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string
     */
    const IOC_NAME = 'dashboard';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function boot()
    {
        $this->app['auth']->extend(
            static::IOC_NAME,
            function ()
            {
                return new DashboardUserProvider( $this->app['db']->connection(), $this->app['hash'], 'user_t' );
            }
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }
}