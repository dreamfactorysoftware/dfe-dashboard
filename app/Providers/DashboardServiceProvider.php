<?php namespace DreamFactory\Enterprise\Dashboard\Providers;

use DreamFactory\Enterprise\Common\Providers\BaseServiceProvider;
use DreamFactory\Enterprise\Dashboard\Services\DashboardService;

class DashboardServiceProvider extends BaseServiceProvider
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /** @inheritdoc */
    const IOC_NAME = 'dfe.dashboard';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //  Register object into instance container
        $this->singleton(static::IOC_NAME, function ($app){
            return new DashboardService($app);
        });
    }
}
