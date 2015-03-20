<?php namespace DreamFactory\Enterprise\Dashboard\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @var array Artisan commands provided by the dashboard
     */
    protected $commands = [];

    /**
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule( Schedule $schedule )
    {
        /**
         * nothing scheduled yet
         */
    }

}
