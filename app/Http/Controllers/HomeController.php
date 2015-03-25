<?php namespace DreamFactory\Enterprise\Dashboard\Http\Controllers;

use DreamFactory\Enterprise\Common\Http\Controllers\BaseController;

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

        $_result = @UserDashboard::processRequest( $_user );
        $_dspList = @UserDashboard::instanceTable( $_user, null, true );
        $_dspListOptions = '<li class="dropdown-header">Your DSPs</li><li>None!</li>';

        $_groupItems = array(
            $this->renderPartial( '_dashboard_item', require __DIR__ . '/_dashboard_new-fabric-dsp.php', true )
        );

        if ( !empty( $_dspList ) )
        {
            $_domain = UserDashboard::getDefaultDomain();
            $_dspListOptions = '<li class="dropdown-header">Your DSPs</li>';

            foreach ( $_dspList as $_dsp )
            {
                $_dspListOptions .= <<<HTML
        <li><a href="https://{$_dsp['instance']->instanceName}{$_domain}" title="Launch this DSP!" target="_blank">{$_dsp['instance']->instanceName}</a></li>
HTML;

                $_groupItems[] = $this->renderPartial( '_dashboard_item', $_dsp, true );
            }
        }

        $_panelGroup = $this->renderPartial(
            '_dashboard_group',
            array(
                'groupId'    => 'dsp_list',
                'groupItems' => array(),
                'groupHtml'  => $_groupItems,
            ),
            true
        );

        if ( null !== ( $_message = Pii::getFlash( 'success' ) ) )
        {
            $_message = <<<HTML
                <div class="alert alert-success fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4>Success!</h4>

                    <p>{$_message}</p>
                </div>
HTML;
        }
        elseif ( null !== ( $_message = Pii::getFlash( 'error' ) ) )
        {
            $_message = <<<HTML
                <div class="alert alert-danger fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4>Fail!</h4>

                    <p>{$_message}</p>
                </div>
HTML;
        }

        require_once __DIR__ . '/_dashboard_import-snapshot.php';

        return view( 'home' );
    }

}
