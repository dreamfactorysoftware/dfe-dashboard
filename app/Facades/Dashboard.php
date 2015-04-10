<?php namespace DreamFactory\Enterprise\Dashboard\Facades;

use DreamFactory\Enterprise\Dashboard\Providers\DashboardServiceProvider;
use DreamFactory\Library\Fabric\Database\Models\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

/**
 * Dashboard
 *
 * @method static mixed handleRequest( Request $request, string $id = null );
 * @method static array|null|string instanceTable( User &$user, array $columns = null, bool $forRender = false )
 * @method static string renderInstance( array $data = [] )
 * @method static string|array renderInstances( array $instances = [], $asArray = true )
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