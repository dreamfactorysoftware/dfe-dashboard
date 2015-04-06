<?php namespace DreamFactory\Enterprise\Dashboard\Http\Controllers;

use DreamFactory\Enterprise\Common\Http\Controllers\BaseController;
use DreamFactory\Enterprise\Dashboard\Facades\Dashboard;
use DreamFactory\Library\Fabric\Database\Models\Auth\User;
use Illuminate\Http\Request;

class HomeController extends BaseController
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function __construct( Request $request )
    {
        parent::__construct( $request );

        //  require auth'd users
        $this->middleware( 'auth' );
    }

    /**
     * @param Request    $request
     * @param string|int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function status( Request $request, $id )
    {
        $_status = Dashboard::handleRequest( $request, $id );

        return response()->json( $_status );
    }

    /**
     * @param Request $request
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function control( Request $request, $id = null )
    {
        $_response = Dashboard::handleRequest( $request, $id );

        return \Redirect::home();
    }

    /**
     * Show the application dashboard to the user.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $_message = isset( $messages ) ? $messages : null;

        /** @type User $_user */
        $_user = \Auth::user();

        $_dspList = Dashboard::instanceTable( $_user, null, true );

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
