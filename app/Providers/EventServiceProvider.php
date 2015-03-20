<?php namespace DreamFactory\Enterprise\Dashboard\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /** @inheritdoc */
    protected $listen = [];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function boot( DispatcherContract $events )
    {
        parent::boot( $events );
    }

}
