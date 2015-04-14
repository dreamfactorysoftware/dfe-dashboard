<?php namespace DreamFactory\Enterprise\Dashboard\Http\Controllers;

use DreamFactory\Enterprise\Common\Http\Controllers\BaseController;
use DreamFactory\Enterprise\Dashboard\Facades\Dashboard;
use DreamFactory\Library\Fabric\Database\Models\Auth\User;
use DreamFactory\Library\Fabric\Database\Models\Deploy\Snapshot;
use Illuminate\Http\Request;

class HomeController extends BaseController
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function __construct( Request $request )
    {
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
        $this->_validateCaptcha( $request );

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
        $_defaultDomain = config( 'dashboard.default-domain' );

        /** @type User $_user */
        $_user = \Auth::user();

        return view(
            'app.home',
            [
                'defaultDomain'    => $_defaultDomain,
                'panelGroup'       => Dashboard::instanceTable( $_user, null, false ),
                'message'          => $_message,
                'isAdmin'          => $_user->admin_ind,
                'displayName'      => $_user->display_name_text,
                'instanceCreator'  => view(
                    'layouts.partials._dashboard_new-rave-instance',
                    ['defaultDomain' => $_defaultDomain,]
                )->render(),
                'snapshotImporter' => view(
                    'layouts.partials._dashboard_import-rave-instance',
                    ['defaultDomain' => $_defaultDomain, 'snapshotList' => $this->_getSnapshotList(),]
                )->render(),
            ]
        );
    }

    /**
     * @return array A list of available snapshots for this user
     */
    protected function _getSnapshotList()
    {
        $_result = [];
        $_rows = Snapshot::where( 'user_id', \Auth::user()->id )->get();

        if ( !empty( $_rows ) )
        {
            /** @var Snapshot[] $_rows */
            foreach ( $_rows as $_row )
            {
                $_result[] = [
                    'id'   => $_row->id,
                    'name' => $_row->snapshot_id_text,
                ];
            }
        }

        return $_result;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function _validateCaptcha( $request )
    {
        //  If captcha is on, check it...
        if ( config( 'dashboard.require-captcha' ) && $request->isMethod( Request::METHOD_POST ) )
        {
            $_validator = \Validator::make(
                \Input::all(),
                [
                    'g-recaptcha-response' => 'required|recaptcha'
                ]
            );

            if ( !$_validator->passes() )
            {
                \Log::error( 'recaptcha failure: ' . print_r( $_validator->errors()->all(), true ) );
                \Session::flash( 'dashboard-failure', 'There was a problem with your request.' );

                return false;
            }
        }

        return true;
    }
}
