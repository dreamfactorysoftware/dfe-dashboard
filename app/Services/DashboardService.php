<?php namespace DreamFactory\Enterprise\Dashboard\Services;

use DreamFactory\Enterprise\Common\Packets\ErrorPacket;
use DreamFactory\Enterprise\Common\Packets\SuccessPacket;
use DreamFactory\Enterprise\Common\Services\BaseService;
use DreamFactory\Enterprise\Common\Traits\EntityLookup;
use DreamFactory\Enterprise\Console\Ops\Providers\OpsClientServiceProvider;
use DreamFactory\Enterprise\Console\Ops\Services\OpsClientService;
use DreamFactory\Enterprise\Dashboard\Enums\DashboardDefaults;
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
    protected $_requireCaptcha = true;
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
    /**
     * @type bool If true, cluster servers are specified from config/dashboard.php
     */
    protected $_useConfigServers = false;
    /**
     * @type int The number of instances to display per row by default
     */
    protected $_instancesPerRow = 3;
    /**
     * @type string The class to wrap columns in
     */
    protected $_columnClass;

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
        $this->_requireCaptcha = config( 'dashboard.require-captcha', true );
        $this->_useConfigServers = config( 'dashboard.override-cluster-servers', false );
        $this->_instancesPerRow = config( 'dashboard.instances-per-row', DashboardDefaults::INSTANCES_PER_ROW );

        if ( $this->_instancesPerRow < 1 )
        {
            $this->_instancesPerRow = 1;
        }
        else if ( $this->_instancesPerRow > 4 )
        {
            $this->_instancesPerRow = 4;
        }

        $this->_determineGridClasses();
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
                if ( $this->_requireCaptcha )
                {
//                    $_captcha = new Captcha();
//                    $_captcha->setPrivateKey( config( 'dashboard.recaptcha.private_key' ) );
//                    $_captcha->timeout = 30;
                }

                switch ( $_command )
                {
                    case 'create':
                        if ( $this->_requireCaptcha )
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
     * @param string $instanceId
     * @param bool   $trial
     * @param bool   $remote If true, create instance on user's account
     *
     * @return bool|mixed|\stdClass
     */
    public function provisionInstance( $instanceId, $trial = false, $remote = false )
    {
        $_provisioner = $this->_request->input( '_provisioner', GuestLocations::DFE_CLUSTER );

        //	Check the name here for quicker UI response...
        if ( false === ( $_instanceName = Instance::isNameAvailable( $instanceId ) ) || is_numeric( $_instanceName[0] ) )
        {
            \Session::flash(
                'dashboard-failure',
                'The name of your instance cannot be "' . $instanceId . '".  It is either currently in-use, or otherwise invalid.'
            );

            return ErrorPacket::make( null, Response::HTTP_BAD_REQUEST, 'Invalid instance name.' );
        }

        if ( false === ( $_clusterConfig = $this->_getClusterConfig() ) )
        {
            \Session::flash(
                'dashboard-failure',
                'Provisioning is not possible at this time. The configured enterprise console for this dashboard is not currently available. Please try your request later.'
            );

            return ErrorPacket::make( null, Response::HTTP_INTERNAL_SERVER_ERROR, 'Cluster server configuration error.' );
        }

        $_payload = array_merge(
            [
                'instance-id'        => $_instanceName,
                'trial'              => $trial,
                'remote'             => $remote,
                'ram-size'           => $this->_request->input( 'ram-size' ),
                'disk-size'          => $this->_request->input( 'disk-size' ),
                'vendor-id'          => $this->_request->input( 'vendor-id' ),
                'vendor-secret'      => $this->_request->input( 'vendor-secret' ),
                'owner-id'           => \Auth::user()->id,
                'guest-location-nbr' => $_provisioner,

            ],
            $_clusterConfig
        );

        $_result = $this->_apiCall( '/ops/provision', $_payload, true );

        if ( is_object( $_result ) )
        {
            if ( $_result->success )
            {
                \Session::flash( 'dashboard-success', 'Instance provisioning requested successfully.' );
            }
            else
            {
                if ( isset( $_result->error ) )
                {
                    $_message = isset( $_result->error->message ) ? $_result->error->message : 'Unknown error';
                }
                else
                {
                    $_message = 'Unknown server error';
                }

                \Session::flash( 'dashboard-failure', $_message );

                return ErrorPacket::make( null, Response::HTTP_INTERNAL_SERVER_ERROR, 'Provisioning error.' );
            }
        }
        else
        {
            \Session::flash(
                'dashboard-failure',
                'Provisioning is not possible at this time. The configured enterprise console for this dashboard is not currently available. Please try your request later.'
            );

            $this->error( 'Error calling ops console api: ' . print_r( $_result, true ) );

            return ErrorPacket::make( null, Response::HTTP_INTERNAL_SERVER_ERROR, 'Cannot connect to ops console.' );
        }

        return SuccessPacket::make( $_result );
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
     * @param       $user
     * @param array $columns
     * @param bool  $forRender
     *
     * @return array|null|string
     */
    public function instanceTable( &$user, $columns = null, $forRender = false )
    {
        $_html = null;
        $_result = $this->getInstances();

        if ( !is_object( $_result ) || !$_result->success )
        {
            \Log::error( 'Error pulling instance list: ' . print_r( $_result, true ) );
            \Session::flash( 'dashboard-failure', 'Error connecting to operations console.' );
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
                        'instance'      => $_model,
                        'groupId'       => 'dsp_list',
                        'targetId'      => $_divId,
                        'targetRel'     => $_model->id,
                        'opened'        => false,
                        'defaultDomain' => $this->_defaultDomain,
                        'statusIcon'    => $_statusIcon,
                        'instanceName'  => $_model->instance_name_text,
                        'targetContent' => $_instanceHtml,
                    );

                    if ( $forRender )
                    {
                        $_html[] = $_item;
                    }
                    else
                    {
                        $_html .= $this->renderInstance( $_item );
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
     *n/
     *
     * @return string
     */
    public function formatInstance( &$instance, $how = null )
    {
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
            $_instanceLinkText = $instance->instance_name_text . $this->_defaultDomain;
            $_instanceLink = '<a href="' . $_instanceLinkText . '" target="_blank" class="dsp-launch-link">' . $instance->instance_name_text . '</a>';
            $_linkLink = '<small><a href="' . $_instanceLinkText . '" target="_blank">' . $_instanceLinkText . '</a>></small>';
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
		<div class="dsp-name">{$_instanceLink}</div>
		<div class="dsp-stats">{$_message}</div>
		<div class="dsp-links">
    		<div class="dsp-controls">{$_html}</div>
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
                'text'    => 'Start',
            ),
            'stop'   => array(
                'enabled' => false,
                'hint'    => 'Stop this DSP',
                'color'   => 'warning',
                'icon'    => 'pause',
                'text'    => 'Stop'
            ),
            'export' => array(
                'enabled' => ProvisionStates::PROVISIONED == $instance->state_nbr,
                'hint'    => 'Make an instance snapshot',
                'color'   => 'info',
                'icon'    => 'cloud-download',
                'text'    => 'Export'
            ),
            'import' => array(
                'enabled' => false,
                'hint'    => 'Restore a snapshot',
                'color'   => 'warning',
                'icon'    => 'cloud-upload',
                'text'    => 'Import',
                'href'    => '#dsp-import-snapshot',
            ),
            'delete' => array(
                'enabled' => false,
                'hint'    => 'Delete instance permanently',
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
                    $_buttons['delete']['enabled'] = false;
                    break;

                case 'stopped':
                    $_buttons['start']['enabled'] = false;
                    $_buttons['stop']['enabled'] = true;
                    $_buttons['export']['enabled'] = false;
                    $_buttons['delete']['enabled'] = true;
                    $_buttons['import']['enabled'] = false;
                    break;

                case 'running':
                    $_buttons['start']['enabled'] = false;
                    $_buttons['stop']['enabled'] = true;
                    $_buttons['export']['enabled'] = true;
                    $_buttons['delete']['enabled'] = true;
                    $_buttons['import']['enabled'] = false;
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
                        $_buttons['start']['enabled'] = false;
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

            if ( $instance->guest_location_nbr == GuestLocations::DFE_CLUSTER &&
                $_buttonName == 'start' &&
                $instance->state_nbr == ProvisionStates::PROVISIONED
            )
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
  <a id="dspcontrol___{$_buttonName}___{$instance->instance_name_text}" class="btn btn-xs btn-{$_button['color']} {$_disabledClass} col-xs-2 col-sm-2" {$_disabled} href="{$_href}" {$_hint}><i class="fa fa-{$_button['icon']}"></i><span class="hidden-sm hidden-xs"> {$_button['text']}</span></a>
HTML;
        }

        $_gettingStartedButton =
            '<a class="btn btn-xs btn-info col-xs-2 col-sm-2 dsp-help-button" id="dspcontrol-' .
            $instance->instance_name_text .
            '" data-placement="middle" title="Help" target="_blank" href="' .
            config( 'dashboard.help-button-url' ) .
            '"><i style="margin-right: 0;" class="fa fa-question-circle"></i></a>';

        $_html = <<<HTML
<div class="btn2-group row">
    {$_html}
    {$_gettingStartedButton}
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
        $_statusIcon = config( 'dashboard.icons.instance-up' );
        $_icon = config( 'dashboard.icons.instance-up' );
        $_message = null;
        $_running = false;

        switch ( $status->state_nbr )
        {
            default:
                $_statusIcon = $_icon = static::SPINNING_ICON;
                $_message =
                    'Your request is being processed.';
                break;

            case ProvisionStates::CREATED:
            case ProvisionStates::PROVISIONING:
                $_statusIcon = $_icon = static::SPINNING_ICON;
                $_message = 'Your instance is being created, with lots of love! You will receive an email when it is ready.';
                break;

            case ProvisionStates::DEPROVISIONING:
                $_statusIcon = $_icon = static::SPINNING_ICON;
                $_message = 'This instance is shutting down.';
                break;

            case ProvisionStates::CREATION_ERROR:
            case ProvisionStates::PROVISIONING_ERROR:
            case ProvisionStates::DEPROVISIONING_ERROR:
                $_message =
                    'There was an error completing your request. Our engineers have been notified. Maybe go take a stroll?';
                $_statusIcon = $_icon = config( 'dashboard.icons.instance-dead' );
                break;

            case ProvisionStates::PROVISIONED:
                $_message = 'Your instance is up and running.';
                $_running = true;
                break;

            case ProvisionStates::DEPROVISIONED:
                $_statusIcon = $_icon = config( 'dashboard.icons.instance-dead' );;
                $_message = 'This DSP is terminated. All you can do is destroy it.';
                break;
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
        if ( empty( $valueToHash ) )
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
        return $this->_requireCaptcha;
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

    /**
     * @param int|string $serverId
     * @param int        $expectedType
     * @param bool       $onlyId If true, only the record's "id" column is returned
     *
     * @return bool
     */
    protected function _ensureServer( $serverId, $expectedType = null, $onlyId = true )
    {
        if ( !empty( $serverId ) )
        {
            $_server = $this->_findServer( $serverId );

            if ( $expectedType && $_server->server_type_id != $expectedType )
            {
                return false;
            }

            return $onlyId ? $_server->id : $_server;
        }

        return null;
    }

    /**
     * Returns an array of provisioning overrides
     *
     * @return array|bool
     */
    protected function _getClusterConfig()
    {
        $_config = [];

        if ( !$this->_useConfigServers )
        {
            return $_config;
        }

        //  Check for a cluster override
        $_clusterId = config( 'dashboard.override-cluster-id' );

        if ( !empty( $_clusterId ) )
        {
            if ( false === ( $_server = $this->_findCluster( $_clusterId ) ) )
            {
                return false;
            }

            //  If you pick a cluster, you get no more choices
            $_config['cluster-id'] = $_server->id;

            return $_config;
        }

        //  Check cluster server overrides
        $_dbServerId = config( 'dashboard.override-db-server-id' );

        if ( false === ( $_serverId = $this->_ensureServer( $_dbServerId, ServerTypes::DB, true ) ) )
        {
            return false;
        }
        else if ( $_serverId )
        {
            $_config['db-server-id'] = $_serverId;
        }

        $_appServerId = config( 'dashboard.override-app-server-id' );

        if ( false === ( $_serverId = $this->_ensureServer( $_appServerId, ServerTypes::APP, true ) ) )
        {
            return false;
        }
        else if ( $_serverId )
        {
            $_config['app-server-id'] = $_serverId;
        }

        $_webServerId = config( 'dashboard.override-web-server-id' );

        if ( false === ( $_serverId = $this->_ensureServer( $_webServerId, ServerTypes::WEB, true ) ) )
        {
            return false;
        }
        else if ( $_serverId )
        {
            $_config['web-server-id'] = $_serverId;
        }

        return $_config;
    }

    /**
     * Renders an instance view
     *
     * @param array $data
     *
     * @return string
     */
    public function renderInstance( $data = [] )
    {
        $_html = '<div class="' . $this->_columnClass . '">' . \View::make( 'layouts.partials._dashboard_item', $data )->render() . '</div>';

        return $_html;
    }

    /**
     * Renders multiple instance views
     *
     * @param array $instances
     * @param bool  $asArray If true, the instances are returned rendered into an array. If false, a single string is returned
     *
     * @return array|string
     */
    public function renderInstances( $instances = [], $asArray = true )
    {
        $_rendered = [];

        foreach ( $instances as $_instance )
        {
            $_rendered[] = $this->renderInstance( $_instance );
        }

        return $asArray ? $_rendered : implode( PHP_EOL, $_rendered );
    }

    protected function _determineGridClasses()
    {
        switch ( $this->_instancesPerRow )
        {
            case 1:
                $this->_columnClass = 'col-xs-12 col-sm-12 col-md-12';
                break;
            case 2:
                $this->_columnClass = 'col-xs-12 col-sm-6 col-md-6';
                break;
            case 3:
                $this->_columnClass = 'col-xs-12 col-sm-6 col-md-3';
                break;
            case 4:
                //  4 per row, col-md-3 x 4 = 12
                $this->_columnClass = 'col-xs-12 col-sm-4 col-md-3';
                break;
            default:
                $this->_columnClass = null;
                break;
        }
    }
}
