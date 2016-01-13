<?php namespace DreamFactory\Enterprise\Dashboard\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        /** Login event listener */
        \Event::listen('auth.login',
            function () {
                \Auth::user()->update([
                    'last_login_date'    => date('c'),
                    'last_login_ip_text' => \Request::server('REMOTE_ADDR'),
                ]);
            });
    }
}
