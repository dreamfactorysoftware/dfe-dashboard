<?php namespace DreamFactory\Enterprise\Dashboard\Facades;

use DreamFactory\Enterprise\Dashboard\Providers\DashboardServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Illuminate\View\View;

/**
 * Dashboard
 *
 * @method static mixed handleRequest( Request $request, string $id = null );
 * @method static array|null|string userInstanceTable( array $data = null, bool $render = false )
 * @method static string renderInstance( $view, array $data = [], string $panel = 'default' )
 * @method static string|array renderInstances( array $instances = [], string $panel = 'default', $asArray = true )
 * @method static array buildInstancePanelData( mixed $instance, $data = [], string $panel = 'default', string $formId = null )
 * @method static mixed panelConfig( string $panel, string $key, mixed $default = null )
 * @method static View|string renderPanel( string $panel, array $data = [], boolean $render = true )
 * @method static array|\stdClass|\stdClass[] getProvisioners()
 */
class Dashboard extends Facade
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return DashboardServiceProvider::IOC_NAME;
    }

}