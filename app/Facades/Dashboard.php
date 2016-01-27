<?php namespace DreamFactory\Enterprise\Dashboard\Facades;

use DreamFactory\Enterprise\Dashboard\Providers\DashboardServiceProvider;
use DreamFactory\Enterprise\Dashboard\Things\InstancePanel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Illuminate\View\View;

/**
 * Dashboard
 *
 * @method static mixed handleRequest(Request $request, $id = null, $extra = null);
 * @method static array|null|string userInstanceTable(array $data = null, $render = false)
 * @method static string renderInstance($view, array $data = [], $panel = 'default')
 * @method static string|array renderInstances(array $instances = [], $panel = 'default', $asArray = true)
 * @method static array buildInstancePanelData($instance, $data = [], $panel = 'default', $formId = null)
 * @method static mixed panelConfig($panel, $key, $default = null)
 * @method static View|string renderPanel($panel, array $data = [], $render = true)
 * @method static array|\stdClass|\stdClass[] getProvisioners()
 * @method static int push(InstancePanel $panel)
 * @method static mixed|null|InstancePanel pop()
 * @method static string renderStack(array $mergeData = [])
 * @method static bool|mixed|\stdClass importInstance($instanceId, $snapshotId, $file = false)
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