<?php namespace DreamFactory\Enterprise\Dashboard\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @var array The artisan commands provided by your application.
     */
    protected $commands = [
        //  Core
        'DreamFactory\Enterprise\Dashboard\Console\Commands\Update',
    ];
}
