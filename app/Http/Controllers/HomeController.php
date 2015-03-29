<?php namespace DreamFactory\Enterprise\Dashboard\Http\Controllers;

use DreamFactory\Enterprise\Common\Http\Controllers\BaseController;
use DreamFactory\Enterprise\Console\Ops\Providers\OpsClientServiceProvider;
use DreamFactory\Enterprise\Dashboard\Services\DashboardService;
use Illuminate\Http\Request;

class HomeController extends BaseController
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * ctor
     */
    public function __construct()
    {
        //  require auth'd users
        $this->middleware( 'auth' );
    }

    public function status( $id )
    {
        $_status = app( OpsClientServiceProvider::IOC_NAME )->status( $id );

        \Log::debug( 'status response: ' . print_r( $_status, true ) );

        return $_status;
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $_tableId = 'platform-table';
        $_shortName = 'Platform';
        $_message = isset( $messages ) ? $messages : null;
        $_user = \Auth::user();
        /** @type DashboardService $_dash */
        $_dash = app( 'dashboard' );
        /** @type Request $_request */
        $_request = app( 'request' );

        $_result = $_dash->handleRequest( $_request );
        $_dspList = $_dash->instanceTable( $_user, null, true );
        $_dspListOptions = '<li class="dropdown-header">Your DSPs</li><li>None!</li>';

        /** @noinspection PhpIncludeInspection */
        $_groupItems = array(
            view(
                'layouts.partials._dashboard_item',
                require base_path( 'resources/views/layouts/partials/_dashboard_new-fabric-dsp.php' )
            )->render()
        );

        if ( !empty( $_dspList ) )
        {
            $_domain = $_dash->getDefaultDomain();
            $_dspListOptions = '<li class="dropdown-header">Your DSPs</li>';

            foreach ( $_dspList as $_dsp )
            {
                $_dspListOptions .= <<<HTML
        <li><a href="https://{$_dsp['instance']->instance_name_text}{$_domain}" title="Launch this DSP!" target="_blank">{$_dsp['instance']->instance_name_text}</a></li>
HTML;

                $_groupItems[] = view( 'layouts.partials._dashboard_item', $_dsp )->render();
            }
        }

        $_panelGroup = view(
            'layouts.partials._dashboard_group',
            [
                'groupId'    => 'dsp_list',
                'groupItems' => [],
                'groupHtml'  => $_groupItems,
            ]
        )->render();

        if ( \Session::has( 'dashboard-success' ) )
        {
            $_flash = \Session::get( 'dashboard-success' );

            $_message = <<<HTML
                <div class="alert alert-success fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4>Success!</h4>

                    <p>{$_flash}</p>
                </div>
HTML;
        }
        elseif ( \Session::has( 'dashboard-failure' ) )
        {
            $_flash = \Session::get( 'dashboard-failure' );

            $_message = <<<HTML
                <div class="alert alert-danger fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4>Fail!</h4>

                    <p>{$_flash}</p>
                </div>
HTML;
        }

        return view( 'home', ['panelGroup' => $_panelGroup, 'message' => $_message] );
    }

}
