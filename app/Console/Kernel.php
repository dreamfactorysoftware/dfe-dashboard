<?php namespace DreamFactory\Enterprise\Dashboard\Console;

use DreamFactory\Enterprise\Common\Traits\CustomLogPath;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use CustomLogPath;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @var array Artisan commands provided by the dashboard
     */
    protected $commands = [];

    //******************************************************************************
    //* Methods
    //******************************************************************************

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
