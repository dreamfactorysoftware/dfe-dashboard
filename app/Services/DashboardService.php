<?php namespace DreamFactory\Enterprise\Dashboard\Services;

use DreamFactory\Enterprise\Common\Services\BaseService;
use DreamFactory\Enterprise\Common\Traits\EntityLookup;
use DreamFactory\Enterprise\Console\Ops\Providers\OpsClientServiceProvider;
use DreamFactory\Enterprise\Console\Ops\Services\OpsClientService;
use DreamFactory\Library\Fabric\Database\Enums\GuestLocations;
use DreamFactory\Library\Fabric\Database\Enums\ProvisionStates;
use DreamFactory\Library\Fabric\Database\Enums\ServerTypes;
use DreamFactory\Library\Fabric\Database\Models\Auth\User;
use DreamFactory\Library\Fabric\Database\Models\Deploy\Instance;
use DreamFactory\Library\Utility\Curl;
use DreamFactory\Library\Utility\IfSet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Psr\Log\LogLevel;

class DashboardService extends BaseService
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use EntityLookup;

    //*************************************************************************
    //* Constants
    //*************************************************************************

    /**
     * @var string
     */
    const SPINNING_ICON = 'fa fa-spinner fa-spin text-warning';

    //*************************************************************************
    //* Variables
    //*************************************************************************

    /**
     * @var string The default sub-domain for new DSPs
     */
    protected $_defaultDomain;
    /**
     * @type bool
     */
    protected $_enableCaptcha = true;
    /**
     * @type string
     */
    protected $_endpoint;
    /**
     * @type string
     */
    protected $_apiKey;
    /**
     * @type Request The request currently being handled
     */
    protected $_request;

    //*************************************************************************
    //* Methods
    //*************************************************************************

    /** @inheritdoc */
    public function __construct( $app = null )
    {
        parent::__construct( $app );

        $this->_defaultDomain = config( 'dashboard.default-domain' );
        $this->_endpoint = config( 'dashboard.api-host' ) . '/' . trim( config( 'dashboard.api-endpoint' ), ' /' );
        $this->_apiKey = config( 'dashboard.api-key' );
        $this->_enableCaptcha = config( 'dashboard.require-captcha', true );
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string|int               $id
     *
     * @return bool|mixed|\stdClass|void
     */
    public function handleRequest( Request $request, $id = null )
    {
        $this->_request = $request;

        $id = $id ?: $request->input( 'id' );

        if ( $request->isMethod( Request::METHOD_POST ) )
        {
            if ( empty( $id ) )
            {
                abort( Response::HTTP_BAD_REQUEST );
            }

            //	Handle the request
            $_command = $request->input( 'control' );

            if ( !empty( $_command ) )
            {
                if ( $this->_enableCaptcha )
                {
//                    $_captcha = new Captcha();
//                    $_captcha->setPrivateKey( config( 'dashboard.recaptcha.private_key' ) );
//                    $_captcha->timeout = 30;
                }

                switch ( $_command )
                {
                    case 'create':
                        if ( $this->_enableCaptcha )
                        {
//                            try
//                            {
//                                //    Check captcha...
//                                if ( !$_captcha->isValid() )
//                                {
//                                    $_captcha->setError();
//                                    throw new CaptchaException( 'Validation code was not entered correctly.' );
//                                }
//                            }
//                            catch ( CaptchaException $_ex )
//                            {
//                                Pii::setFlash( 'dashboard-failure', $_ex->getMessage() );
//
//                                return false;
//                            }
                        }

                        $this->provisionInstance( $id, true, false );
                        break;

                    case 'create-remote':
                        $this->provisionInstance( $id, false, true );
                        break;

                    case 'destroy':
                    case 'delete':
                        $this->deprovisionInstance( $id );
                        break;

                    case 'start':
                        $this->startInstance( $id );
                        break;

                    case 'stop':
                        $this->stopInstance( $id );
                        break;

                    case 'export':
                    case 'snapshot':
                        $this->exportInstance( $id );
                        break;

                    case 'snapshots':
                        $this->_instanceSnapshots( $id );
                        break;

                    case 'migrate':
                    case 'import':
                        $this->importInstance( $id );
                        break;

                    case 'status':
                        return $this->_instanceStatus( $id );
                }
            }

            return true;
        }

        $this->_request = null;

        return false;
    }

    /**
     * @param string $id
     *
     * @return array|\stdClass
     */
    protected function _instanceStatus( $id )
    {
        $_response = $this->_getOpsClient()->status( $id );

        if ( $_response->success )
        {
            $_status = $_response->response;
        }
        else
        {
            $_status = new \stdClass();
            $_status->deleted = false;
            $_status->state_nbr = ProvisionStates::CREATION_ERROR;
            $_status->instance_name_text = $id;
        }

        $_status->deleted = false;
        $_status->icons = $this->getStatusIcon( $_status );
        $_status->buttons = $this->getDspControls( $_status );

        if ( ProvisionStates::PROVISIONED == $_status->state_nbr )
        {
            $_status->link =
                '<a href="https://' .
                $_status->instance_name_text .
                $this->_defaultDomain .
                '" target="_blank" class="dsp-launch-link">' .
                $_status->instance_name_text .
                '</a>';
        }

        return $_status;
    }

    /**
     * @param string|int $instanceId
     *
     * @return bool|mixed|\stdClass
     */
    public function deprovisionInstance( $instanceId )
    {
        $_result = $this->_apiCall( '/ops/destroy', array('instance-id' => $instanceId), true );

        if ( $_result->success )
        {
            \Session::flash( 'dashboard-success', 'Instance deprovisioning requested successfully.' );
        }
        else
        {
            \Session::flash( 'dashboard-failure', $_result->message );
        }

        return $_result;
    }

    /**
     * @param string $instanceId
     * @param bool   $trial
     * @param bool   $remote If true, create instance on user's account
     *
     * @return bool|mixed|\stdClass
     */
    public function provisionInstance( $instanceId, $trial = false, $remote = false )
    {
        //	Clean up the name
        if ( false === ( $_instanceName = Instance::isNameAvailable( $instanceId ) ) || is_numeric( $_instanceName[0] ) )
        {
            abort( Response::HTTP_BAD_REQUEST, 'Your DSP name is invalid. It must begin with a letter (A-Z)' );
        }

        //	Only admins can have a dsp without the prefix
        $_cluster = $this->_findCluster( config( 'dashboard.cluster-id' ) );
        $_dbServer = $this->_findServer( config( 'dashboard.db-server-id' ) );

        if ( $_dbServer->server_type_id !== ServerTypes::DB )
        {
            abort( Response::HTTP_INTERNAL_SERVER_ERROR, 'Database server invalid.' );
        }

        $_payload = array(
            'instance-id'   => $_instanceName,
            'cluster-id'    => $_cluster->id,
            'db-server-id'  => $_dbServer->id,
            'trial'         => $trial,
            'remote'        => $remote,
            'ram-size'      => $this->_request->input( 'ram-size' ),
            'disk-size'     => $this->_request->input( 'disk-size' ),
            'vendor-id'     => $this->_request->input( 'vendor-id' ),
            'vendor-secret' => $this->_request->input( 'vendor-secret' ),
        );

        $_result = $this->_apiCall( '/ops/create', $_payload, true );

        if ( $_result->success )
        {
            \Session::flash( 'dashboard-success', 'Instance provisioning requested successfully.' );
        }
        else
        {
            \Session::flash( 'dashboard-failure', $_result->message );
        }

        return $_result;
    }

    /**
     * @param string $instanceId
     *
     * @return bool|mixed|\stdClass
     */
    public function stopInstance( $instanceId )
    {
        return $this->_apiCall( '/ops/stop/' . $instanceId, [], true );
    }

    /**
     * @param string $instanceId
     *
     * @return bool|mixed|\stdClass
     */
    public function startInstance( $instanceId )
    {
        return $this->_apiCall( '/ops/start/' . $instanceId, [], true );
    }

    /**
     * @param string $instanceId
     * @param bool   $trial
     *
     * @return bool|mixed|\stdClass
     */
    public function exportInstance( $instanceId, $trial = true )
    {
        return $this->_apiCall( '/ops/export/' . $instanceId );
    }

    /**
     * @param string $instanceId
     *
     * @return bool|mixed|\stdClass
     */
    protected function _instanceSnapshots( $instanceId )
    {
        $_result = $this->_apiCall( '/ops/exports/' . $instanceId );

        if ( !$_result->success )
        {
            \Session::flash( 'dashboard-failure', $_result->message );

            return null;
        }

        $_html = null;

        foreach ( $_result->details as $_name => $_snapshots )
        {
            $_html .= '<optgroup label="' . $_name . '">';

            /** @var $_snapshots \stdClass[] */
            foreach ( $_snapshots as $_snapshot )
            {
                $_date = date( 'F j, Y @ H:i:s', strtotime( $_snapshot->date ) );

                $_html .=
                    '<option id="' .
                    $_snapshot->snapshot_id .
                    '" value="' .
                    $_snapshot->snapshot_id .
                    '" name="' .
                    $_snapshot->snapshot_id .
                    '">' .
                    $_date .
                    '</option>';
            }

            $_html .= '</optgroup>';
        }

        return $_html;
    }

    /**
     * @param string $instanceId
     *
     * @return bool|mixed|\stdClass
     */
    public function importInstance( $instanceId )
    {
        $_snapshot = $this->_request->input( 'dsp-snapshot-list' );

        if ( empty( $_snapshot ) )
        {
            \Session::flash( 'dashboard-failure', 'No snapshot selected to import.' );

            return false;
        }

        //	Strip off the name if there...
        if ( false !== strpos( $_snapshot, '.' ) )
        {
            $_parts = explode( '.', $_snapshot );

            if ( 2 != count( $_parts ) || false === strtotime( $_parts[1] ) )
            {
                \Session::flash( 'dashboard-failure', 'Invalid snapshot ID' );

                return false;
            }

            $_snapshot = $_parts[1];
        }

        return $this->_apiCall( '/ops/import/' . $instanceId, array('snapshot' => $_snapshot), true );
    }

    /**
     * @return bool|mixed|\stdClass
     */
    public function getInstances()
    {
        $_response = $this->_getOpsClient()->instances();

        $this->log( LogLevel::DEBUG, 'instances response: ' . print_r( $_response, true ) );

        return $_response;
    }

    /**
     * @param      $user
     * @param null $columns
     * @param bool $forRender
     *
     * @return array|null|string
     */
    public function instanceTable( &$user, $columns = null, $forRender = false )
    {
        $_html = null;
        $_result = $this->getInstances();

        if ( !$_result->success )
        {
            \Log::error( 'Error pulling instance list: ' . print_r( $_result, true ) );
            \Session::flash( 'dashboard-failure', $_result->message );
        }
        else
        {
            if ( isset( $_result->response ) )
            {
                /** @var \stdClass $_model */
                foreach ( $_result->response as $_dspName => $_model )
                {
                    if ( !isset( $_model, $_model->id ) )
                    {
                        continue;
                    }

                    list( $_divId, $_instanceHtml, $_statusIcon ) = $this->formatInstance( $_model );

                    $_item = array(
                        'instance'       => $_model,
                        'groupId'        => 'dsp_list',
                        'targetId'       => $_divId,
                        'targetRel'      => $_model->id,
                        'opened'         => count( $_result->response ),
                        'triggerContent' => <<<HTML
<div class="instance-heading-dsp-name">{$_model->instance_name_text}<span class="text-muted">{$this->_defaultDomain}</div>
<div class="instance-heading-status pull-right"><i class="fa fa-fw {$_statusIcon} fa-2x"></i></div>
HTML
                        ,
                        'targetContent'  => $_instanceHtml,
                    );

                    if ( $forRender )
                    {
                        $_html[] = $_item;
                    }
                    else
                    {
                        $_html .= \View::make( 'layouts.partials._dashboard_item', $_item )->render();
                    }

                    unset( $_model );
                }
            }
        }

        return $_html;
    }

    /**
     * @param \stdClass $instance
     * @param int       $how
     *
     * @return string
     */
    public function formatInstance( &$instance, $how = null )
    {
        $_gettingStartedButton =
            '<a class="btn btn-xs btn-info dsp-help-button" id="dspcontrol-' .
            $instance->instance_name_text .
            '" data-placement="left" title="Help" target="_blank" href="' .
            config( 'dashboard.help-button-url' ) .
            '"><i class="fa fa-question-circle"></i></a>';

        list( $_icon, $_statusIcon, $_message, $_running ) = $this->getStatusIcon( $instance );

        if ( empty( $instance->instance_id_text ) )
        {
            $instance->instance_id_text = 'NEW';
        }

        $_divId = $this->divId( 'dsp', $instance );

        $_instanceLinkText = $_linkLink = null;
        $_html = $this->getDspControls( $instance, $_buttons );

        if ( $instance->state_nbr == ProvisionStates::PROVISIONED )
        {
            $_instanceLinkText = 'https://' . $instance->instance_name_text . $this->_defaultDomain;
            $_instanceLink =
                '<a href="' .
                $_instanceLinkText .
                '" target="_blank" class="dsp-launch-link">' .
                $instance->instance_name_text .
                '</a>';
            $_linkLink = '<a href="' . $_instanceLinkText . '" target="_blank">' . $_instanceLinkText . '</a>';
        }
        else
        {
            $_instanceLink = $instance->instance_name_text;
        }

        if ( $this->_isIconClass( $_icon ) )
        {
            $_icon = '<i class="fa ' . $_icon . ' fa-3x"></i>';
        }

        $_html = <<<HTML
	<div class="dsp-icon well pull-left dsp-real text-success">{$_icon}</div>
	<div class="dsp-info">
		<div class="dsp-name">{$_instanceLink}<small>{$_linkLink}</small></div>
		<div class="dsp-stats">{$_message}</div>
		<div class="dsp-links">
		<span class="dsp-controls pull-left">{$_html}</span>
			{$_gettingStartedButton}
		</div>
	</div>
HTML;

        return array($_divId, $_html, $_statusIcon, $_instanceLinkText);
    }

    /**
     * Formats the button panel for an individual DSP
     *
     * @param \stdClass $instance
     * @param array     $buttons
     *
     * @return string
     */
    public function getDspControls( $instance, &$buttons = null )
    {
        $_buttons = array(
            'start'  => array(
                'enabled' => false,
                'hint'    => 'Start this DSP',
                'color'   => 'success',
                'icon'    => 'play',
                'text'    => 'Start'
            ),
            'stop'   => array(
                'enabled' => false,
                'hint'    => 'Stop this DSP',
                'color'   => 'warning',
                'icon'    => 'pause',
                'text'    => 'Stop'
            ),
            'export' => array(
                'enabled' => false,
                'hint'    => 'Make a portable DSP backup',
                'color'   => 'info',
                'icon'    => 'cloud-download',
                'text'    => 'Backup'
            ),
            'import' => array(
                'enabled' => false,
                'hint'    => 'Restore a portable backup',
                'color'   => 'warning',
                'icon'    => 'cloud-upload',
                'text'    => 'Restore',
                'href'    => '#dsp-import-snapshot',
            ),
            'delete' => array(
                'enabled' => false,
                'hint'    => 'Delete this DSP permanently',
                'color'   => 'danger',
                'icon'    => 'trash',
                'text'    => 'Destroy!'
            ),
        );

        if ( isset( $instance->vendorStateName ) && !empty( $instance->vendorStateName ) )
        {
            switch ( $instance->vendorStateName )
            {
                case 'terminated':
                    $_buttons['start']['enabled'] = false;
                    $_buttons['stop']['enabled'] = false;
                    $_buttons['export']['enabled'] = false;
                    $_buttons['import']['enabled'] = false;
                    $_buttons['delete']['enabled'] = true;
                    break;

                case 'stopped':
                    $_buttons['start']['enabled'] = true;
                    $_buttons['stop']['enabled'] = false;
                    $_buttons['export']['enabled'] = true;
                    $_buttons['delete']['enabled'] = true;
                    $_buttons['import']['enabled'] = false;
                    break;

                case 'running':
                    $_buttons['start']['enabled'] = false;
                    $_buttons['stop']['enabled'] = true;
                    $_buttons['export']['enabled'] = true;
                    $_buttons['delete']['enabled'] = true;
                    $_buttons['import']['enabled'] = true;
                    break;
            }
        }
        else
        {
            switch ( $instance->state_nbr )
            {
                case ProvisionStates::PROVISIONED:
                    //	Not queued for deprovisioning
                    if ( 1 != $instance->deprovision_ind )
                    {
                        $_buttons['stop']['enabled'] = true;
                        $_buttons['export']['enabled'] = true;
                        $_buttons['import']['enabled'] = true;
                        $_buttons['delete']['enabled'] = true;
                    }
                    break;

                case ProvisionStates::DEPROVISIONED:
                    //	Not queued for reprovisioning
                    if ( 1 != $instance->provision_ind )
                    {
                        $_buttons['start']['enabled'] = true;
                        $_buttons['export']['enabled'] = true;
                        $_buttons['delete']['enabled'] = true;
                        $_buttons['import']['enabled'] = false;
                    }
                    break;
            }
        }

        $buttons = $_buttons;

        $_html = null;

        //	No stop for hosted instances...
        unset( $_buttons['stop'] );

        foreach ( $_buttons as $_buttonName => $_button )
        {
            $_hint = null;
            $_disabledClass = 'disabled';
            $_disabled = ( !$_button['enabled'] ? 'disabled="disabled"' : $_disabledClass = null );

            if ( !$_disabled && null !== ( $_hint = IfSet::get( $_button, 'hint' ) ) )
            {
                $_hint = 'data-toggle="tooltip" title="' . $_hint . '"';
            }

            if ( ( !isset( $instance->vendor_id ) || GuestLocations::DFE_CLUSTER == $instance->vendor_id ) && $_buttonName == 'start' )
            {
                $_href = config( 'dashboard.default-domain-protocol', 'https' ) . '://' . $instance->instance_name_text . $this->_defaultDomain;
                $_button['text'] = 'Launch!';
                $_disabled = $_disabledClass = null;
                $_buttonName = 'launch';
            }
            else
            {
                $_href = isset( $_button['href'] ) ? $_button['href'] : '#';
            }

            $_html .= <<<HTML
  <a id="dspcontrol___{$_buttonName}___{$instance->instance_name_text}" class="btn btn-sm btn-{$_button['color']} {$_disabledClass}" {$_disabled} href="{$_href}" {$_hint}><i class="fa fa-{$_button['icon']}"></i> {$_button['text']}</a>
HTML;
        }

        $_html = <<<HTML
<div class="btn2-group">
{$_html}
</div>
HTML;

        return $_html;
    }

    /**
     * @param \stdClass $status
     * @param bool      $key
     *
     * @return array
     */
    public function getStatusIcon( $status, $key = false )
    {
        $_statusIcon = 'fa-rocket';
        $_icon = 'fa-rocket';
        $_message = null;
        $_running = false;

        if ( $key )
        {
            $_statusIcon = 'fa-key';
            $_message = null;
        }
        else
        {

            if ( isset( $status->vendorStateName ) && null !== $status->vendorStateName )
            {
                switch ( $status->vendorStateName )
                {
                    case 'stopped':
                        $_statusIcon = $_icon = 'fa-stop';
                        $_message = 'This DSP is stopped. Click the Start button to restart.';
                        break;

                    case 'terminated':
                        $_statusIcon = $_icon = 'fa-ambulance';
                        $_message = 'This DSP is terminated. All you can do is destroy it.';
                        break;

                    case 'shutting-down':
                    case 'stopping':
                        $_statusIcon = $_icon = static::SPINNING_ICON;
                        $_message = 'This DSP is being stopped.';
                        break;

                    case 'pending':
                        $_statusIcon = $_icon = static::SPINNING_ICON;
                        $_message =
                            'This DSP is being prepared for the requested operation. Be cool baby, it\'ll be done in a sec.';
                        break;

                    case 'running':
                        $_message = 'This DSP is alive and well. Click on the name above to launch.';
                        $_running = true;
                        break;
                }
            }
            else
            {
                switch ( $status->state_nbr )
                {
                    case ProvisionStates::CREATED:
                        $_statusIcon = $_icon = static::SPINNING_ICON;
                        $_message = 'This DSP request has been received and is queued for creation.';
                        break;

                    case ProvisionStates::PROVISIONING:
                        $_statusIcon = $_icon = static::SPINNING_ICON;
                        $_message =
                            'Your DSP is being carefully assembled with lots of love. You will receive an email when it is ready.';
                        break;

                    case ProvisionStates::PROVISIONED:
                        //	Queued for deprovisioning
                        if ( 1 == $status->deprovision_ind )
                        {
                            $_statusIcon = $_icon = static::SPINNING_ICON;
                        }
                        $_message = 'This DSP is alive and well. Click on the name above to launch.';
                        $_running = true;
                        break;

                    case ProvisionStates::DEPROVISIONING:
                        $_statusIcon = $_icon = static::SPINNING_ICON;
                        $_message =
                            'This DSP is being destroyed. You will receive an email when it has been destroyed.';
                        break;

                    case ProvisionStates::DEPROVISIONED:
                        $_icon = '<img src="/img/icon-deprovisioned.png" class="fa fa-3x">';
                        $_statusIcon = 'fa-exclamation-triangle';
                        $_message =
                            'This DSP is being destroyed. You will receive an email when it has been destroyed.';
                        break;

                    case ProvisionStates::DEPROVISIONING_ERROR:
                    case ProvisionStates::PROVISIONING_ERROR:
                        $_message =
                            'There was an error issuing your request. Our engineers have been notified. Maybe go take a stroll?';
                        $_statusIcon = $_icon = 'fa-ambulance';
                        break;
                }
            }
        }

        return array($_icon, $_statusIcon, $_message, $_running);
    }

    /**
     * @param string $url
     * @param array  $payload
     * @param bool   $returnAll
     *
     * @param string $method
     *
     * @return bool|mixed|\stdClass
     */
    protected function _apiCall( $url, $payload = [], $returnAll = true, $method = Request::METHOD_POST )
    {
        $_payload = $this->_addTokenToPayload( $payload );
        $_response = Curl::request( $method, $this->_endpoint . '/' . trim( $url, '/' ), $_payload );

        if ( $_response && is_object( $_response ) && isset( $_response->success ) )
        {
            return $returnAll ? $_response : $_response->details;
        }

        //	Error and redirect
        \Session::flash(
            'dashboard-failure',
            'An unexpected situation has occurred with your request. Please try again in a few minutes, or email <a href="mailto:support@dreamfactory.com">support@dreamfactory.com</a>.'
        );

        if ( is_string( $_response ) )
        {
            echo $_response;
        }

        return false;
    }

    /**
     * @param string $prefix
     * @param object $instance
     * @param bool   $key
     *
     * @return string
     */
    public function divId( $prefix, $instance, $key = false )
    {
        return
            $prefix .
            '___' .
            $instance->id .
            /*$this->hashId( $instance->id ) .*/
            '___' .
            ( $key ? $instance->label : $instance->instance_name_text );
    }

    /**
     * Get a hashed id suitable for framing
     *
     * @param string $valueToHash
     *
     * @return string
     */
    public function hashId( $valueToHash )
    {
        if ( null === $valueToHash )
        {
            return null;
        }

        return hash( 'sha256', config( 'dashboard.api-key' ) . $valueToHash );
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return \Auth::user();
    }

    /**
     * Builds a list of enabled providers based on files in the templates directory
     *
     * @return string
     */
    public function buildProviderList()
    {
    }

    /**
     * Determine if a class contains a FontAwesome icon (v3+)
     *
     * @param string $class
     *
     * @return bool
     */
    protected function _isIconClass( $class )
    {
        return ( 'icon-' == substr( $class, 0, 5 ) ||
            'fa-' == substr( $class, 0, 3 ) ||
            $class == static::SPINNING_ICON );
    }

    /**
     * @return string
     */
    public function getDefaultDomain()
    {
        return $this->_defaultDomain;
    }

    /**
     * @return boolean
     */
    public function isEnableCaptcha()
    {
        return $this->_enableCaptcha;
    }

    protected function _addTokenToPayload( $payload )
    {
        $_id = config( 'dashboard.client-id' );
        $_secret = config( 'dashboard.client-secret' );

        return array_merge(
            array(
                'user-id'      => \Auth::user()->id,
                'client-id'    => $_id,
                'access-token' => hash_hmac( 'sha256', $_id, $_secret )
            ),
            $payload ?: []
        );

    }

    /**
     * @return OpsClientService
     */
    protected function _getOpsClient()
    {
        return $this->app[OpsClientServiceProvider::IOC_NAME]
            ?: function ()
            {
                throw new \RuntimeException(
                    'The enterprise console api service is not available.'
                );
            };
    }
}
