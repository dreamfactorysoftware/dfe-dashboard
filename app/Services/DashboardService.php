<?php namespace DreamFactory\Enterprise\Dashboard\Services;

use DreamFactory\Enterprise\Common\Enums\AppKeyClasses;
use DreamFactory\Enterprise\Common\Packets\ErrorPacket;
use DreamFactory\Enterprise\Common\Packets\SuccessPacket;
use DreamFactory\Enterprise\Common\Services\BaseService;
use DreamFactory\Enterprise\Common\Traits\EntityLookup;
use DreamFactory\Enterprise\Console\Ops\Providers\OpsClientServiceProvider;
use DreamFactory\Enterprise\Console\Ops\Services\OpsClientService;
use DreamFactory\Enterprise\Dashboard\Enums\DashboardDefaults;
use DreamFactory\Enterprise\Dashboard\Enums\PanelTypes;
use DreamFactory\Enterprise\Dashboard\Facades\Dashboard;
use DreamFactory\Enterprise\Database\Enums\GuestLocations;
use DreamFactory\Enterprise\Database\Enums\ProvisionStates;
use DreamFactory\Enterprise\Database\Enums\ServerTypes;
use DreamFactory\Enterprise\Database\Models\Instance;
use DreamFactory\Enterprise\Database\Models\User;
use DreamFactory\Library\Utility\IfSet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class DashboardService extends BaseService
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use EntityLookup;

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
    protected $_panelsPerRow = 3;
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

        $this->_defaultDomain =
            '.' . trim( config( 'dashboard.default-dns-zone' ), '.' ) . '.' . trim( config( 'dashboard.default-dns-domain' ), '.' );
        $this->_useConfigServers = config( 'dfe.ops-client.override-cluster-servers', false );
        $this->_requireCaptcha = config( 'dashboard.require-captcha', true );
        $this->_panelsPerRow = config( 'dashboard.panels-per-row', DashboardDefaults::PANELS_PER_ROW );

        $this->_determineGridLayout();
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
        $_command = $request->input( 'control' );

        if ( $request->isMethod( Request::METHOD_POST ) )
        {
            if ( empty( $id ) || empty( $_command ) )
            {

                $this->_request = null;

                return ErrorPacket::make( null, Response::HTTP_BAD_REQUEST );
            }

            switch ( $_command )
            {
                case 'provision':
                case 'launch':
                case 'create':
                    $this->provisionInstance( $id, true, false );
                    break;

                case 'create-remote':
                    $this->provisionInstance( $id, false, true );
                    break;

                case 'destroy':
                case 'delete':
                case 'deprovision':
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
        $_status->panelIcons = $this->_getPanelIcons( $_status );

        if ( ProvisionStates::PROVISIONED == $_status->state_nbr )
        {
            $_status->link = $this->_buildInstanceLink( $_status );
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

        $_result = $this->_apiCall( 'provision', $_payload );

        if ( $_result && is_object( $_result ) && isset( $_result->success ) )
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
        $_result = $this->_apiCall( 'deprovision', ['instance-id' => $instanceId] );

        if ( $_result && is_object( $_result ) && isset( $_result->success ) )
        {
            \Session::flash( 'dashboard-success', 'Instance deprovisioning requested successfully.' );
        }
        else
        {
            \Session::flash( 'dashboard-failure', 'Garbled response from console.' );
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
        return $this->_apiCall( 'stop', ['instance-id' => $instanceId] );
    }

    /**
     * @param string $instanceId
     *
     * @return bool|mixed|\stdClass
     */
    public function startInstance( $instanceId )
    {
        return $this->_apiCall( 'start', ['instance-id' => $instanceId] );
    }

    /**
     * @param string $instanceId
     * @param bool   $trial
     *
     * @return bool|mixed|\stdClass
     */
    public function exportInstance( $instanceId )
    {
        return $this->_apiCall( 'export', ['instance-id' => $instanceId] );
    }

    /**
     * @param string $instanceId
     *
     * @return bool|mixed|\stdClass
     */
    protected function _instanceSnapshots( $instanceId )
    {
        $_result = $this->_apiCall( 'exports', ['instance-id' => $instanceId] );

        if ( !$_result || !is_object( $_result ) || !isset( $_result->success ) )
        {
            \Session::flash( 'dashboard-failure', isset( $_result, $_result->message ) ? $_result->message : 'An unknown error occurred.' );

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

        return $this->_apiCall( 'import', ['instance-id' => $instanceId, 'snapshot' => $_snapshot] );
    }

    /**
     * @return bool|mixed|\stdClass
     */
    public function getInstances()
    {
        $_response = $this->_getOpsClient()->instances();

        //$this->log( LogLevel::DEBUG, 'instances response: ' . print_r( $_response, true ) );

        return $_response;
    }

    /**
     * @return bool|mixed|\stdClass
     */
    public function getProvisioners()
    {
        $_response = $this->_getOpsClient()->provisioners();

        //$this->log( LogLevel::DEBUG, 'instances response: ' . print_r( $_response, true ) );

        if ( !isset( $_response->response ) )
        {
            \Log::error( '  * Provisioner bogus response: ' . print_r( $_response, true ) );

            return [];
        }

        return $_response;
    }

    /**
     * @param array $data   Any data needed to build the table
     * @param bool  $render If true, the rendered HTML is returned as a string
     *
     * @return array|null|string
     */
    public function instanceTable( $data = [], $render = false )
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
            if ( isset( $_result->response ) && !empty( $_result->response ) )
            {
                /** @var \stdClass $_model */
                foreach ( $_result->response as $_dspName => $_model )
                {
                    if ( !isset( $_model, $_model->id ) )
                    {
                        continue;
                    }

                    $_instance = $this->_buildInstancePanel( $_model, $data, PanelTypes::SINGLE );

                    if ( $render )
                    {
                        $_html .= $_instance->render();
                    }
                    else
                    {
                        $_html[] = $_instance;
                    }

                    unset( $_model );
                }
            }
        }

        return $_html;
    }

    /**
     * Create an HTML <A> with a link to the given instance
     *
     * @param \stdClass|Instance $status
     *
     * @return string
     */
    protected function _buildInstanceLink( $status )
    {
        return
            '<a href="https://' . $status->instance_name_text . $this->_defaultDomain . '" ' .
            'target="_blank" class="dsp-launch-link">' . $status->instance_name_text . '</a>';
    }

    /**
     * @param \stdClass|Instance $instance
     * @param array              $data
     * @param string             $panel The panel to use if not "default"
     * @param bool               $rendered
     *
     * @return \Illuminate\View\View|string
     */
    protected function _buildInstancePanel( $instance, $data = [], $panel = 'default', $rendered = false )
    {
        $_viewData = $this->buildInstancePanelData( $instance, $data, $panel );
        $_viewName = $this->panelConfig( $panel, 'template', DashboardDefaults::DEFAULT_INSTANCE_BLADE );
        $_view = \View::make( $_viewName, $_viewData, ['instance' => $instance] );

        return $rendered ? $_view->render() : $_view;
    }

    /**
     * Provides the array of data necessary to populate an individual instance panel
     *
     * @param \stdClass|Instance $instance
     * @param array              $data
     * @param string             $formId The id of the inner panel form
     * @param string             $panel  The type panel. Can be "default", "create", or "import"
     *
     * @return array
     */
    public function buildInstancePanelData( $instance, $data = [], $panel = 'default', $formId = null )
    {
        $_overrides = [];

        if ( empty( $data ) || !is_array( $data ) )
        {
            $data = [];
        }

        if ( empty( $panel ) )
        {
            $panel = 'default';
        }

        if ( empty( $formId ) )
        {
            $formId = $this->panelConfig( $panel, 'form-id', 'form-' . $panel );
        }

        $_name = is_object( $instance ) ? $instance->instance_name_text : 'NEW';
        $_id = is_object( $instance ) ? $instance->id : 0;

        $_overrides['headerIcon'] = $this->panelConfig( $panel, 'header-icon' );
        $_overrides['headerIconSize'] = $this->panelConfig( $panel, 'header-icon-size', 'fa-1x' );
        $_overrides['headerStatusIcon'] = $this->panelConfig( $panel, 'header-status-icon' );
        $_overrides['headerStatusIconSize'] = $this->panelConfig( $panel, 'header-status-icon-size' );
        $_overrides['instanceStatusIcon'] = $this->panelConfig( $panel, 'status-icon' );
        $_overrides['instanceStatusIconSize'] = $this->panelConfig( $panel, 'status-icon-size' );
        $_overrides['instanceStatusIconContext'] = $this->panelConfig( $panel, 'status-icon-context' );

        $_key = $this->panelConfig( $panel, 'description' );

        if ( $_key != ( $_panelDescription = \Lang::get( $_key ) ) )
        {
            $_overrides['panelDescription'] = $_panelDescription;
        }

        return array_merge(
            [
                'collapse'               => false,
                'panelType'              => $panel,
                'panelContext'           => $this->panelConfig( $panel, 'context', DashboardDefaults::PANEL_CONTEXT ),
                'instanceName'           => $_name,
                'panelTitle'             => $_name,
                'headerIcon'             => IfSet::get( $data, 'header-icon' ),
                'headerIconSize'         => IfSet::get( $data, 'header-icon-size' ),
                'formId'                 => $formId,
                'captchaId'              => 'dfe-rc-' . $_name,
                'panelSize'              => $this->_columnClass,
                'toolbarButtons'         => $this->_getToolbarButtons( $instance ),
                'panelButtons'           => $this->_getToolbarButtons( $instance ),
                'instanceLinks'          => [],//$this->_getInstanceLinks( $instance ),
                'defaultDomain'          => $this->_defaultDomain,
                'instanceDivId'          => $this->createDivId( 'instance', $_id, $_name ),
                'instanceStatusIcon'     => $this->panelConfig( $panel, 'status-icon' ),
                'instanceStatusIconSize' => $this->panelConfig( $panel, 'status-icon-size' ),
                'instanceStatusContext'  => $this->panelConfig( $panel, 'status-icon-context' ),
                'instanceUrl'            => config( 'dashboard.default-domain-protocol', 'https' ) .
                    '://' .
                    $_name .
                    $this->_defaultDomain,
            ],
            $this->_getInstanceStatus( $instance ),
            $data,
            $_overrides
        );
    }

    /**
     * @param \stdClass|Instance $instance
     *
     * @return array
     */
    protected function _getInstanceStatus( $instance )
    {
        $_spinner = config( 'dashboard.icons.spinner', DashboardDefaults::SPINNING_ICON );

        switch ( $instance->state_nbr )
        {
            case ProvisionStates::CREATED:
                $_icon = $_spinner;
                $_context = 'text-success';
                $_text = \Lang::get( 'dashboard.status-started' );
                break;

            case ProvisionStates::PROVISIONING:
                $_icon = $_spinner;
                $_context = 'text-info';
                $_text = \Lang::get( 'dashboard.status-started' );
                break;

            case ProvisionStates::PROVISIONED:
                $_icon = config( 'dashboard.icons.up' );
                $_context = 'text-success';
                $_text = \Lang::get( 'dashboard.status-up' );
                break;

            case ProvisionStates::DEPROVISIONING:
                $_icon = $_spinner;
                $_context = 'text-info';
                $_text = \Lang::get( 'dashboard.status-stopping' );
                break;

            case ProvisionStates::DEPROVISIONED:
                $_icon = config( 'dashboard.icons.terminating' );
                $_context = 'text-warning';
                $_text = \Lang::get( 'dashboard.status-terminating' );
                break;

            case ProvisionStates::PROVISIONING_ERROR:
            case ProvisionStates::DEPROVISIONING_ERROR:
            case ProvisionStates::CREATION_ERROR:
                $_icon = config( 'dashboard.icons.dead' );
                $_context = 'text-danger';
                $_text = \Lang::get( 'dashboard.status-dead' );
                break;

            default:
                $_icon = config( 'dashboard.icons.unknown' );
                $_context = 'text-warning';
                $_text = \Lang::get( 'dashboard.status-dead' );
                break;
        }

        return [
            'headerIcon'            => $_icon,
            'instanceStatusIcon'    => $_icon,
            'instanceStatusContext' => $_context,
            'instanceStatusText'    => $_text,
        ];

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

            if ( GuestLocations::DFE_CLUSTER == $instance->guest_location_nbr &&
                'start' == $_buttonName &&
                ProvisionStates::PROVISIONED == $instance->state_nbr
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
        $_spinner = config( 'dashboard.icons.spinner' );
        $_message = null;
        $_running = false;

        switch ( $status->state_nbr )
        {
            default:
                $_statusIcon = $_icon = $_spinner;
                $_message =
                    'Your request is being processed.';
                break;

            case ProvisionStates::CREATED:
            case ProvisionStates::PROVISIONING:
                $_statusIcon = $_icon = $_spinner;
                $_message = 'Your instance is being created, with lots of love! You will receive an email when it is ready.';
                break;

            case ProvisionStates::DEPROVISIONING:
                $_statusIcon = $_icon = $_spinner;
                $_message = 'This instance is shutting down.';
                break;

            case ProvisionStates::CREATION_ERROR:
            case ProvisionStates::PROVISIONING_ERROR:
            case ProvisionStates::DEPROVISIONING_ERROR:
                $_message =
                    'There was an error completing your request. Our engineers have been notified. Maybe go take a stroll?';
                $_statusIcon = $_icon = config( 'dashboard.icons.dead' );
                break;

            case ProvisionStates::PROVISIONED:
                $_message = 'Your instance is up and running.';
                $_running = true;
                $_statusIcon = $_icon = config( 'dashboard.icons.up' );
                break;

            case ProvisionStates::DEPROVISIONED:
                $_statusIcon = $_icon = config( 'dashboard.icons.dead' );;
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
        /** @type OpsClientService $_service */
        $_service = app( OpsClientServiceProvider::IOC_NAME );
        $_response = $_service->any( $url, $payload, [], $method );

        if ( $_response && is_object( $_response ) && isset( $_response->success ) )
        {
            return $returnAll ? $_response : $_response->response;
        }

        //	Error and redirect
        \Session::flash(
            'dashboard-failure',
            'An unexpected situation has occurred with your request. Please try again in a few minutes, or email <a href="mailto:support@dreamfactory.com">support@dreamfactory.com</a>.'
        );

        if ( is_string( $_response ) )
        {
            \Log::error( 'Console API call received unexpected result: ' . $_response );
        }

        return false;
    }

    /**
     * @param string  $prefix
     * @param integer $id
     * @param string  $name
     *
     * @return string
     * @internal param object $instance
     * @internal param bool $key
     *
     */
    public function createDivId( $prefix, $id, $name )
    {
        return implode( '___', [$prefix, $id, $name] );
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
        $_user = \Auth::user();
        $_key = $_user->getAppKey( $_user->id, AppKeyClasses::USER );

        return array_merge(
            array(
                'client-id'    => $_key->client_id,
                'access-token' => hash_hmac( 'sha256', $_key->client_id, $_key->client_secret )
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
            //  Return an empty array allowing the console to decide where to place the instance
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
     * @param array|\stdClass|Instance $instance
     * @param array                    $data  Any data needed to build the table
     * @param string                   $panel The type panel. Can be "default", "create", or "import"
     *
     * @return string
     */
    public function renderInstance( $instance, $data = [], $panel = 'default' )
    {
        return $this->_buildInstancePanel( $instance, $data, $panel, true );
    }

    /**
     * Renders multiple instance views
     *
     * @param array  $instances
     * @param array  $data    Any data needed to build the table
     * @param string $panel   The type panel. Can be "default", "create", or "import"
     * @param bool   $asArray If true, the instances are returned rendered into an array. If false, a single string is returned
     *
     * @return array|string
     */
    public function renderInstances( $instances = [], $data = [], $panel = 'default', $asArray = true )
    {
        $_rendered = [];

        foreach ( $instances as $_instance )
        {
            $_rendered[] = $this->renderInstance( $_instance, $data, $panel );
        }

        return $asArray ? $_rendered : implode( PHP_EOL, $_rendered );
    }

    /**
     * Based on the configured number of instances per row, set the appropriate grid classes.
     */
    protected function _determineGridLayout()
    {
        if ( $this->_panelsPerRow < 1 )
        {
            $this->_panelsPerRow = 1;
        }
        else if ( $this->_panelsPerRow > 6 )
        {
            $this->_panelsPerRow = 6;
        }

        switch ( $this->_panelsPerRow )
        {
            case 1:
                $this->_columnClass = 'col-xs-12 col-sm-12 col-md-12';
                break;

            case 2:
                $this->_columnClass = 'col-xs-6 col-sm-6 col-md-6';
                break;

            case 3:
                $this->_columnClass = 'col-xs-12 col-sm-3 col-md-4';
                break;

            default:
            case 4:
                //  4 per row, col-md-3 x 4 = 12
                $this->_columnClass = 'col-xs-12 col-sm-4 col-md-3';
                break;

            case 6:
                //  6 per row, col-md-2 x 6 = 12
                $this->_columnClass = 'col-xs-12 col-sm-6 col-md-2';
                break;
        }
    }

    /**
     * @param \stdClass $status
     *
     * @return array [:icon, :message]
     */
    protected function _getPanelIcons( $status )
    {
        $_message = null;
        $_spinner = config( 'dashboard.icons.spinner', DashboardDefaults::SPINNING_ICON );

        switch ( $status->state_nbr )
        {
            case ProvisionStates::CREATION_ERROR:
            case ProvisionStates::PROVISIONING_ERROR:
            case ProvisionStates::DEPROVISIONING_ERROR:
                $_icon = config( 'dashboard.icons.dead' );
                $_message = \Lang::get( 'dashboard.status-error' );
                break;

            case ProvisionStates::CREATED:
            case ProvisionStates::PROVISIONING:
                $_icon = $_spinner;
                $_message = \Lang::get( 'dashboard.status-starting' );
                break;

            case ProvisionStates::DEPROVISIONING:
                $_icon = $_spinner;
                $_message = \Lang::get( 'dashboard.status-stopping' );
                break;

            case ProvisionStates::PROVISIONED:
                $_icon = config( 'dashboard.icons.up' );
                $_message = \Lang::get( 'dashboard.status-up' );
                break;

            case ProvisionStates::DEPROVISIONED:
                $_icon = config( 'dashboard.icons.dead' );;
                $_message = \Lang::get( 'dashboard.status-dead' );
                break;

            default:
                $_icon = $_spinner;
                $_message = \Lang::get( 'dashboard.status-other' );
                break;
        }

        return array('icon' => $_icon, 'message' => $_message);
    }

    /**
     * @param \stdClass|Instance $instance
     *
     * @return array
     */
    protected function _getPanelButtons( $instance )
    {
        $_buttons = [
            'launch' => ['context' => 'btn-success', 'icon' => 'fa-play', 'hint' => 'Launch your instance', 'text' => 'Launch'],
            //            'stop'   => ['context' => 'btn-warning', 'icon' => 'fa-stop', 'hint' => 'Stop your instance', 'text' => 'Stop'],
            'export' => ['context' => 'btn-info', 'icon' => 'fa-cloud-download', 'hint' => 'Create an export of your instance', 'text' => 'Export'],
            'import' => ['context' => 'btn-warning', 'icon' => 'fa-cloud-upload', 'hint' => 'Import a prior export', 'text' => 'Import'],
            'delete' => ['context' => 'btn-danger', 'icon' => 'fa-times', 'hint' => 'Permanently destroy this instance', 'text' => 'Destroy'],
            'help'   => [
                'id'      => 'instance-control-' . $instance->instance_name_text,
                'context' => 'btn-danger',
                'icon'    => 'fa-times',
                'hint'    => 'Documentation and Support',
                'text'    => null,
            ],
        ];

        return $_buttons;
    }

    protected function _makeToolbarButton( $id, $text, array $options = [] )
    {
        static $_template = ['type' => 'button', 'size' => 'btn-xs', 'context' => 'btn-info', 'icon' => '', 'hint' => '', 'data' => []];

        if ( isset( $options['icon'] ) )
        {
            $options['icon'] = '<i class="fa fa-fw ' . $options['icon'] . ' instance-toolbar-button"></i>';
        }

        if ( !isset( $options['hint'] ) )
        {
            $options['hint'] = $text . ' instance';
        }

        $_action = str_replace( ['_', ' '], '-', trim( strtolower( $text ) ) );

        return array_merge(
            $_template,
            [
                'id'   => 'instance-' . $_action . '-' . $id,
                'text' => $text,
                'data' => [
                    'instance-id'     => $id,
                    'instance-action' => $_action,
                ],
            ],
            $options
        );
    }

    /**
     * @param \stdClass|Instance $instance
     *
     * @return array
     * @todo generate buttons based on provisioner features rather than statically
     */
    protected function _getToolbarButtons( $instance )
    {
        $_id = $instance->instance_id_text;

        if ( GuestLocations::DFE_CLUSTER == $instance->guest_location_nbr )
        {
            $_buttons = [
                'launch' => $this->_makeToolbarButton( $_id, 'Launch', ['context' => 'btn-success', 'icon' => 'fa-play'] ),
                'delete' => $this->_makeToolbarButton( $_id, 'Delete', ['context' => 'btn-danger', 'icon' => 'fa-times'] ),
                'export' => $this->_makeToolbarButton( $_id, 'Export', ['context' => 'btn-info', 'icon' => 'fa-cloud-download'] ),
                'import' => $this->_makeToolbarButton( $_id, 'Import', ['context' => 'btn-warning', 'icon' => 'fa-cloud-upload'] ),
            ];
        }
        else
        {
            $_buttons = [
                'start'     => $this->_makeToolbarButton( $_id, 'Start', ['context' => 'btn-success', 'icon' => 'fa-play',] ),
                'stop'      => $this->_makeToolbarButton( $_id, 'Stop', ['context' => 'btn-warning', 'icon' => 'fa-stop',] ),
                'terminate' => $this->_makeToolbarButton( $_id, 'Terminate', ['context' => 'btn-danger', 'icon' => 'fa-times',] ),
                'export'    => $this->_makeToolbarButton( $_id, 'Export', ['context' => 'btn-info', 'icon' => 'fa-cloud-download',] ),
                'import'    => $this->_makeToolbarButton( $_id, 'Import', ['context' => 'btn-warning', 'icon' => 'fa-cloud-upload',] ),
            ];
        }

        return $_buttons;
    }

    /**
     * @param string $panel
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function panelConfig( $panel, $key, $default = null )
    {
        return config( 'dashboard.panels.' . $panel . '.' . $key, $default );
    }

    /**
     * @param string $panel  The panel to render
     * @param array  $data   Any additional view data
     * @param bool   $render If true, view is rendered and html is returned
     *
     * @return View|string
     */
    public function renderPanel( $panel, $data = [], $render = true )
    {
        if ( !PanelTypes::contains( $panel = $panel ?: DashboardDefaults::DEFAULT_PANEL ) )
        {
            throw new \InvalidArgumentException( 'The panel type "' . $panel . '" is invalid.' );
        }

        $_blade = $this->panelConfig( $panel, 'template', DashboardDefaults::SINGLE_INSTANCE_BLADE );

        $_offeringsHtml = null;

        $_dudes = Dashboard::getProvisioners();

        if ( !is_object( $_dudes ) )
        {
            throw new \RuntimeException( 'Invalid response from the console.' );
        }

        if ( $_dudes->success )
        {
            foreach ( $_dudes->response as $_host )
            {
                if ( $_host->id == 'rave' )
                {
                    if ( isset( $_host->offerings ) )
                    {
                        foreach ( $_host->offerings as $_tag => $_offering )
                        {
                            $_data = (array)$_offering;
                            $_displayName = IfSet::get( $_data, 'name', $_tag );
                            $_items = IfSet::get( $_data, 'items', [] );
                            $_suggested = IfSet::get( $_data, 'suggested' );

                            $_helpBlock =
                                ( null !== ( $_helpBlock = IfSet::get( $_data, 'help-block' ) ) )
                                    ? '<p class="help-block">' . $_helpBlock . '</p>'
                                    : null;

                            if ( !empty( $_items ) )
                            {
                                $_options = null;

                                foreach ( $_items as $_name => $_config )
                                {
                                    $_attributes = $_html = $_selected = null;

                                    $_config = (array)$_config;

                                    if ( null === ( $_description = IfSet::get( $_config, 'description' ) ) )
                                    {
                                        $_description = $_name;
                                    }
                                    else
                                    {
                                        unset( $_config['description'] );
                                    }

                                    foreach ( $_config as $_key => $_value )
                                    {
                                        $_key = str_replace( ['"', '\'', '_', ' ', '.', ','], '-', strtolower( $_key ) );
                                        $_attributes .= ' data-' . $_key . '="' . $_value . '" ';
                                    }

                                    $_suggested == $_name && $_selected = ' selected ';
                                    $_options .= '<option value="' .
                                        $_name .
                                        '" ' .
                                        $_attributes .
                                        ' ' .
                                        $_selected .
                                        '>' .
                                        $_description .
                                        '</option>';
                                }

                                $_html = <<<HTML
<div class="form-group">
    <label for="{$_tag}" class="col-md-2 control-label" style="white-space: nowrap; text-end-overflow:  hidden;">{$_displayName}</label>
    <div class="col-md-6">
    <select id="{$_tag}" name="{$_tag}" class="form-control">
        {$_options}
    </select>
    </div>
    {$_helpBlock}
</div>
HTML;

                                $_offeringsHtml .= $_html;
                            }
                        }
                    }
                }
            }
        }

        $_description = \Lang::get( $this->panelConfig( $panel, 'description' ) );

        if ( empty( $_description ) )
        {
            $_description = null;
        }

        if ( PanelTypes::SINGLE == $panel )
        {
            $data['panelSize'] = IfSet::get( $data, 'panelSize', $this->_columnClass );
        }

        $_view = view(
            $_blade,
            array_merge(
                $data,
                [
                    'panelTitle'       => \Lang::get( 'dashboard.instance-' . $panel . '-title' ),
                    'panelType'        => $panel,
                    'formId'           => 'form-' . $panel,
                    'panelContext'     => $this->panelConfig( $panel, 'context' ),
                    'headerIcon'       => $this->panelConfig( $panel, 'header-icon' ),
                    'panelDescription' => $_description,
                    'offerings'        => $_offeringsHtml,
                ]
            )
        );

        return $render ? $_view->render() : $_view;
    }
}
