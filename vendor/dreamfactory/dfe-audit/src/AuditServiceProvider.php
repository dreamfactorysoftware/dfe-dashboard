<?php namespace DreamFactory\Enterprise\Services\Auditing;

use DreamFactory\Enterprise\Common\Providers\BaseServiceProvider;

/**
 * Register the auditing service as a provider with Laravel.
 *
 * To use the "Audit" facade for this provider, you need to add the service provider to
 * your the providers array in your app/config/app.php file:
 *
 *  'providers' => array(
 *
 *      ... Other Providers Above ...
 *      'DreamFactory\Enterprise\Services\Auditing\Providers\AuditServiceProvider',
 *
 *  ),
 */
class AuditServiceProvider extends BaseServiceProvider
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string The name of the service in the IoC
     */
    const IOC_NAME = 'dfe.audit';

    //********************************************************************************
    //* Public Methods
    //********************************************************************************

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //  Register object into instance container
        $this->singleton(static::IOC_NAME,
            function ($app){
                return new AuditingService($app);
            });
    }
}
