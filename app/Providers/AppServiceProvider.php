<?php namespace DreamFactory\Enterprise\Dashboard\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function register()
    {
        $this->app->bind('Illuminate\Contracts\Auth\Registrar', 'DreamFactory\Enterprise\Dashboard\Services\Registrar');
    }
}
