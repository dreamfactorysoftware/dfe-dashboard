<?php namespace DreamFactory\Enterprise\Dashboard\Http\Controllers;

use DreamFactory\Enterprise\Common\Http\Controllers\BaseController;
use DreamFactory\Enterprise\Dashboard\Providers\DashboardServiceProvider;
use DreamFactory\Enterprise\Dashboard\Services\DashboardService;
use DreamFactory\Library\Fabric\Database\Models\Auth\User;
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
        $_status = app( DashboardServiceProvider::IOC_NAME )->handleRequest( app( 'request' ), $id );

        return response()->json( $_status );
    }

    public function control( Request $request, $id = null )
    {
        return
            response()->json( app( 'dashboard' )->handleRequest( $request, $id ) );
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
        /** @type User $_user */
        $_user = \Auth::user();
        /** @type DashboardService $_dash */
        $_dash = app( 'dashboard' );
        $_domain = $_dash->getDefaultDomain();

        /** @type Request $_request */
        $_request = app( 'request' );

        $_result = $_dash->handleRequest( $_request );
        $_dspList = $_dash->instanceTable( $_user, null, true );

        /** @noinspection PhpIncludeInspection */
        $_groupItems = array(
            view(
                'layouts.partials._dashboard_item',
                require base_path( 'resources/views/layouts/partials/_dashboard_new-fabric-dsp.php' )
            )->render()
        );

        if ( !empty( $_dspList ) )
        {
            foreach ( $_dspList as $_dsp )
            {
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

        return view(
            'app.home',
            [
                'panelGroup'  => $_panelGroup,
                'message'     => $_message,
                'isAdmin'     => $_user->admin_ind,
                'displayName' => $_user->display_name_text
            ]
        );
    }

}
