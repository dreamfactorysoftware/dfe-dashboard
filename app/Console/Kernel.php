<?php namespace DreamFactory\Enterprise\Dashboard\Console;

use DreamFactory\Enterprise\Common\Traits\CustomLogPath;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string
     */
    const CLASS_TO_REPLACE = 'Illuminate\Foundation\Bootstrap\ConfigureLogging';
    /**
     * @type string
     */
    const REPLACEMENT_CLASS = 'DreamFactory\Enterprise\Common\Bootstrap\CommonLoggingConfiguration';

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

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function __construct( Application $app, Dispatcher $events )
    {
        $this->_replaceLoggingConfigurationClass( static::CLASS_TO_REPLACE, static::REPLACEMENT_CLASS );

        parent::__construct( $app, $events );
    }
}
