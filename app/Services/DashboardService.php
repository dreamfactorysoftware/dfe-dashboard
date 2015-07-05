<?php namespace DreamFactory\Enterprise\Dashboard\Services;

use DreamFactory\Enterprise\Common\Enums\ServerTypes;
use DreamFactory\Enterprise\Common\Packets\ErrorPacket;
use DreamFactory\Enterprise\Common\Packets\SuccessPacket;
use DreamFactory\Enterprise\Common\Services\BaseService;
use DreamFactory\Enterprise\Common\Traits\EntityLookup;
use DreamFactory\Enterprise\Console\Ops\Providers\OpsClientServiceProvider;
use DreamFactory\Enterprise\Console\Ops\Services\OpsClientService;
use DreamFactory\Enterprise\Dashboard\Enums\DashboardDefaults;
use DreamFactory\Enterprise\Dashboard\Enums\PanelTypes;
use DreamFactory\Enterprise\Dashboard\Facades\Dashboard;
use DreamFactory\Enterprise\Dashboard\Things\InstancePanel;
use DreamFactory\Enterprise\Database\Enums\GuestLocations;
use DreamFactory\Enterprise\Database\Enums\OwnerTypes;
use DreamFactory\Enterprise\Database\Enums\ProvisionStates;
use DreamFactory\Enterprise\Database\Models\Instance;
use DreamFactory\Enterprise\Database\Models\User;
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
    /**
     * @type InstancePanel[] The panel stack
     */
    protected $_panels = [];

    //*************************************************************************
    //* Methods
    //*************************************************************************

    /** @inheritdoc */
    public function __construct($app = null)
    {
        parent::__construct($app);

        $this->_useConfigServers = config('dashboard.override-cluster-servers', false);
        $this->_requireCaptcha = config('dashboard.require-captcha', true);
        $this->setDefaultDomain(
            implode('.',
                [
                    trim(config('dashboard.default-dns-zone'), '.'),
                    trim(config('dashboard.default-dns-domain'), '.'),
                ])
        );

        $this->_determineGridLayout();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string|int               $id
     *
     * @return bool|mixed|\stdClass|void
     */
    public function handleRequest(Request $request, $id = null)
    {
        $this->_request = $request;

        $id = $id ?: $request->input('id');
        $_command = $request->input('control');

        if ($request->isMethod(Request::METHOD_POST)) {
            if (empty($id) || empty($_command)) {

                $this->_request = null;

                return ErrorPacket::make(null, Response::HTTP_BAD_REQUEST);
            }

            switch ($_command) {
                case 'provision':
                case 'launch':
                case 'create':
                    $this->provisionInstance($id, true, false);
                    break;

                case 'create-remote':
                    $this->provisionInstance($id, false, true);
                    break;

                case 'deprovision':
                case 'destroy':
                case 'delete':
                    $this->deprovisionInstance($id);
                    break;

                case 'start':
                    $this->startInstance($id);
                    break;

                case 'stop':
                    $this->stopInstance($id);
                    break;

                case 'export':
                case 'snapshot':
                    $this->exportInstance($id);
                    break;

                case 'snapshots':
                    $this->_instanceSnapshots($id);
                    break;

//                case 'migrate':
//                case 'import':
//                    $this->importInstance($id);
//                    break;

                case 'status':
                    return $this->_instanceStatus($id);
            }
        }

        return true;
    }

    /**
     * @param string $id
     *
     * @return array|\stdClass
     */
    protected function _instanceStatus($id)
    {
        $_response = $this->_getOpsClient()->status($id);

        if ($_response->success) {
            $_status = $_response->response;
        } else {
            $_status = new \stdClass();
            $_status->deleted = false;
            $_status->state_nbr = ProvisionStates::UNKNOWN;
            $_status->instance_name_text = $id;
        }

        $_status->deleted = false;
        $_status->icons = $this->getStatusIcon($_status);
        $_status->buttons = $this->getDspControls($_status);
        $_status->panelIcons = $this->_getPanelIcons($_status);

        if (ProvisionStates::PROVISIONED == $_status->state_nbr) {
            $_status->link = $this->_buildInstanceLink($_status);
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
    public function provisionInstance($instanceId, $trial = false, $remote = false)
    {
        $_provisioner = $this->_request->input('_provisioner', GuestLocations::DFE_CLUSTER);

        //	Check the name here for quicker UI response...
        if (false === ($_instanceName = Instance::isNameAvailable($instanceId)) || is_numeric($_instanceName[0])) {
            \Session::flash(
                'dashboard-failure',
                'The name of your instance cannot be "' . $instanceId . '".  It is either currently in-use, or otherwise invalid.'
            );

            return ErrorPacket::make(null, Response::HTTP_BAD_REQUEST, 'Invalid instance name.');
        }

        if (false === ($_clusterConfig = $this->_getClusterConfig())) {
            \Session::flash(
                'dashboard-failure',
                'Provisioning is not possible at this time. The configured enterprise console for this dashboard is not currently available. Please try your request later.'
            );

            return ErrorPacket::make(null, Response::HTTP_INTERNAL_SERVER_ERROR, 'Cluster server configuration error.');
        }

        $_payload = array_merge(
            [
                'instance-id'        => $_instanceName,
                'trial'              => $trial,
                'remote'             => $remote,
                'ram-size'           => $this->_request->input('ram-size'),
                'disk-size'          => $this->_request->input('disk-size'),
                'vendor-id'          => $this->_request->input('vendor-id'),
                'vendor-secret'      => $this->_request->input('vendor-secret'),
                'owner-id'           => \Auth::user()->id,
                'owner-type'         => OwnerTypes::USER,
                'guest-location-nbr' => $_provisioner,

            ],
            $_clusterConfig
        );

        $_result = $this->_apiCall('provision', $_payload);

        if ($_result && is_object($_result) && isset($_result->success)) {
            if ($_result->success) {
                \Session::flash('dashboard-success', 'Instance provisioning requested successfully.');
            } else {
                if (isset($_result->error)) {
                    $_message = isset($_result->error->message) ? $_result->error->message : 'Unknown error';
                } else {
                    $_message = 'Unknown server error';
                }

                \Session::flash('dashboard-failure', $_message);

                return ErrorPacket::make(null, Response::HTTP_INTERNAL_SERVER_ERROR, 'Provisioning error.');
            }
        } else {
            \Session::flash(
                'dashboard-failure',
                'Provisioning is not possible at this time. The configured enterprise console for this dashboard is not currently available. Please try your request later.'
            );

            $this->error('Error calling ops console api: ' . print_r($_result, true));

            return ErrorPacket::make(null, Response::HTTP_INTERNAL_SERVER_ERROR, 'Cannot connect to ops console.');
        }

        return SuccessPacket::make($_result);
    }

    /**
     * @param string|int $instanceId
     *
     * @return bool|mixed|\stdClass
     */
    public function deprovisionInstance($instanceId)
    {
        $_result = $this->_apiCall('deprovision', ['instance-id' => $instanceId]);

        if ($_result && is_object($_result) && isset($_result->success)) {
            \Session::flash('dashboard-success', 'Instance deprovisioning requested successfully.');
        } else {
            \Session::flash('dashboard-failure', 'Garbled response from console.');
        }

        return $_result;
    }

    /**
     * @param string $instanceId
     *
     * @return bool|mixed|\stdClass
     */
    public function stopInstance($instanceId)
    {
        return $this->_apiCall('stop', ['instance-id' => $instanceId]);
    }

    /**
     * @param string $instanceId
     *
     * @return bool|mixed|\stdClass
     */
    public function startInstance($instanceId)
    {
        return $this->_apiCall('start', ['instance-id' => $instanceId]);
    }

    /**
     * @param string $instanceId
     *
     * @return bool|mixed|\stdClass
     * @internal param bool $trial
     *
     */
    public function exportInstance($instanceId)
    {
        return $this->_apiCall('export', ['instance-id' => $instanceId]);
    }

    /**
     * @param string $instanceId
     *
     * @return bool|mixed|\stdClass
     */
    protected function _instanceSnapshots($instanceId)
    {
        $_result = $this->_apiCall('exports', ['instance-id' => $instanceId]);

        if (!$_result || !is_object($_result) || !isset($_result->success)) {
            \Session::flash('dashboard-failure',
                isset($_result, $_result->message) ? $_result->message : 'An unknown error occurred.');

            return null;
        }

        $_html = null;

        foreach ($_result->details as $_name => $_snapshots) {
            $_html .= '<optgroup label="' . $_name . '">';

            /** @var $_snapshots \stdClass[] */
            foreach ($_snapshots as $_snapshot) {
                $_date = date('F j, Y @ H:i:s', strtotime($_snapshot->date));

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
    public function importInstance($instanceId)
    {
        $_snapshot = $this->_request->input('dsp-snapshot-list');

        if (empty($_snapshot)) {
            \Session::flash('dashboard-failure', 'No snapshot selected to import.');

            return false;
        }

        //	Strip off the name if there...
        if (false !== strpos($_snapshot, '.')) {
            $_parts = explode('.', $_snapshot);

            if (2 != count($_parts) || false === strtotime($_parts[1])) {
                \Session::flash('dashboard-failure', 'Invalid snapshot ID');

                return false;
            }

            $_snapshot = $_parts[1];
        }

        return $this->_apiCall('import', ['instance-id' => $instanceId, 'snapshot' => $_snapshot]);
    }

    /**
     * @return bool|mixed|\stdClass
     */
    public function getInstances()
    {
        return $this->_getOpsClient()->instances();
    }

    /**
     * @return bool|mixed|\stdClass
     */
    public function getProvisioners()
    {
        static $_provisioners;

        return $_provisioners ?: $_provisioners = $this->_getOpsClient()->provisioners();
    }

    /**
     * Builds the table of user instances
     *
     * @param array $data   Any data needed to build the table
     * @param bool  $render If true, the rendered HTML is returned as a string
     *
     * @return array|null|string
     */
    public function userInstanceTable($data = [], $render = false)
    {
        $_result = $this->getInstances();

        if (!is_object($_result) || !is_bool($_result->success)) {
            \Log::error('Error pulling instance list: ' . print_r($_result, true));
            \Session::flash('dashboard-failure', 'Error connecting to operations console.');

            return null;
        }

        if (!isset($_result->response) || empty($_result->response)) {
            return null;
        }

        $_html = null;

        /** @var \stdClass $_model */
        foreach ($_result->response as $_dspName => $_model) {
            if (!isset($_model, $_model->id)) {
                continue;
            }

            $_instance = $this->_buildInstancePanel($_model, $data, PanelTypes::SINGLE);

            if ($render) {
                $_html .= $_instance->render();
            } else {
                $_html[] = $_instance;
            }

            unset($_model, $_dspName);
        }

        return $_html;
    }

    /**
     * Provides the array of data necessary to populate an individual instance panel
     *
     * @param \stdClass|Instance $instance
     * @param array              $data
     * @param string             $formId    The id of the inner panel form
     * @param string             $panelType The type panel. Can be "default", "create", or "import"
     *
     * @return array
     */
    public function buildInstancePanelData($instance, $data = [], $panelType = 'default', $formId = null)
    {
        (empty($data) || !is_array($data)) && $data = [];
        empty($panelType) && $panelType = 'default';
        empty($formId) && $formId = $this->panelConfig($panelType, 'form-id', 'form-' . $panelType);

        $_name = is_object($instance) ? $instance->instance_name_text : 'NEW';
        $_id = is_object($instance) ? $instance->id : 0;
        $_overrides = $this->_getPanelOverrides($panelType);

        return array_merge(
            [
                //  Defaults
                'headerIcon'             => array_get($data, 'header-icon'),
                'headerIconSize'         => array_get($data, 'header-icon-size'),
                'instanceLinks'          => [],//$this->_getInstanceLinks( $instance ),
                'toolbarButtons'         => $this->_getToolbarButtons($instance),
                'panelButtons'           => $this->_getToolbarButtons($instance),
                'instanceDivId'          => $this->createDivId('instance', $_id, $_name),
                'instanceStatusIcon'     => $this->panelConfig($panelType, 'status-icon'),
                'instanceStatusIconSize' => $this->panelConfig($panelType, 'status-icon-size'),
                'instanceStatusContext'  => $this->panelConfig($panelType, 'status-icon-context'),
                'instanceUrl'            => $this->buildInstanceUrl($instance->instance_name_text),
            ],
            //  Instance status
            $this->_getInstanceStatus($instance),
            //  Merge data
            $data,
            //  Overrides
            $_overrides,
            //  ENSURE!
            [
                'captchaId'     => 'dfe-rc-' . $_name,
                'formId'        => $formId,
                'defaultDomain' => $this->_defaultDomain,
                'panelSize'     => $this->_columnClass,
                'panelTitle'    => $_name,
                'panelType'     => $panelType,
                'collapse'      => false,
                'instanceName'  => $_name,
            ]
        );
    }

    /**
     * @param \stdClass|Instance $instance
     *
     * @return array
     */
    protected function _getInstanceStatus($instance)
    {
        $_spinner = config('icons.spinner', DashboardDefaults::SPINNING_ICON);

        switch ($instance->state_nbr) {
            case ProvisionStates::CREATED:
                $_icon = $_spinner;
                $_context = 'text-success';
                $_text = \Lang::get('dashboard.status-started');
                break;

            case ProvisionStates::PROVISIONING:
                $_icon = $_spinner;
                $_context = 'text-info';
                $_text = \Lang::get('dashboard.status-started');
                break;

            case ProvisionStates::PROVISIONED:
                $_icon = config('icons.up');
                $_context = 'text-success';
                $_text = \Lang::get('dashboard.status-up');
                break;

            case ProvisionStates::DEPROVISIONING:
                $_icon = $_spinner;
                $_context = 'text-info';
                $_text = \Lang::get('dashboard.status-stopping');
                break;

            case ProvisionStates::DEPROVISIONED:
                $_icon = config('icons.terminating');
                $_context = 'text-warning';
                $_text = \Lang::get('dashboard.status-terminating');
                break;

            case ProvisionStates::PROVISIONING_ERROR:
            case ProvisionStates::DEPROVISIONING_ERROR:
            case ProvisionStates::CREATION_ERROR:
                $_icon = config('icons.dead');
                $_context = 'text-danger';
                $_text = \Lang::get('dashboard.status-dead');
                break;

            default:
                $_icon = config('icons.unknown');
                $_context = 'text-warning';
                $_text = \Lang::get('dashboard.status-dead');
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
    public function getDspControls($instance, &$buttons = null)
    {
        $_buttons = [
            'start'  => [
                'enabled' => false,
                'hint'    => 'Start this DSP',
                'color'   => 'success',
                'icon'    => 'play',
                'text'    => 'Start',
            ],
            'stop'   => [
                'enabled' => false,
                'hint'    => 'Stop this DSP',
                'color'   => 'warning',
                'icon'    => 'pause',
                'text'    => 'Stop',
            ],
            'export' => [
                'enabled' => ProvisionStates::PROVISIONED == $instance->state_nbr,
                'hint'    => 'Make an instance snapshot',
                'color'   => 'info',
                'icon'    => 'cloud-download',
                'text'    => 'Export',
            ],
            //            'import' => [
            //                'enabled' => false,
            //                'hint'    => 'Restore a snapshot',
            //                'color'   => 'warning',
            //                'icon'    => 'cloud-upload',
            //                'text'    => 'Import',
            //                'href'    => '#dsp-import-snapshot',
            //            ],
            'delete' => [
                'enabled' => false,
                'hint'    => 'Delete instance permanently',
                'color'   => 'danger',
                'icon'    => 'trash',
                'text'    => 'Destroy!',
            ],
        ];

        if (isset($instance->vendorStateName) && !empty($instance->vendorStateName)) {
            switch ($instance->vendorStateName) {
                case 'terminated':
                    $_buttons['start']['enabled'] = false;
                    $_buttons['stop']['enabled'] = false;
                    $_buttons['export']['enabled'] = false;
//                    $_buttons['import']['enabled'] = false;
                    $_buttons['delete']['enabled'] = false;
                    break;

                case 'stopped':
                    $_buttons['start']['enabled'] = false;
                    $_buttons['stop']['enabled'] = true;
                    $_buttons['export']['enabled'] = false;
                    $_buttons['delete']['enabled'] = true;
//                    $_buttons['import']['enabled'] = false;
                    break;

                case 'running':
                    $_buttons['start']['enabled'] = false;
                    $_buttons['stop']['enabled'] = true;
                    $_buttons['export']['enabled'] = true;
                    $_buttons['delete']['enabled'] = true;
//                    $_buttons['import']['enabled'] = false;
                    break;
            }
        } else {
            switch ($instance->state_nbr) {
                case ProvisionStates::PROVISIONED:
                    //	Not queued for deprovisioning
                    if (1 != $instance->deprovision_ind) {
                        $_buttons['stop']['enabled'] = true;
                        $_buttons['export']['enabled'] = true;
//                        $_buttons['import']['enabled'] = true;
                        $_buttons['delete']['enabled'] = true;
                        $_buttons['start']['enabled'] = false;
                    }
                    break;

                case ProvisionStates::DEPROVISIONED:
                    //	Not queued for reprovisioning
                    if (1 != $instance->provision_ind) {
                        $_buttons['start']['enabled'] = true;
                        $_buttons['export']['enabled'] = true;
                        $_buttons['delete']['enabled'] = true;
//                        $_buttons['import']['enabled'] = false;
                    }
                    break;
            }
        }

        $buttons = $_buttons;

        $_html = null;

        //	No stop for hosted instances...
        unset($_buttons['stop']);

        foreach ($_buttons as $_buttonName => $_button) {
            $_hint = null;
            $_disabledClass = 'disabled';
            $_disabled = (!$_button['enabled'] ? 'disabled="disabled"' : $_disabledClass = null);

            if (!$_disabled && null !== ($_hint = array_get($_button, 'hint'))) {
                $_hint = 'data-toggle="tooltip" title="' . $_hint . '"';
            }

            if (GuestLocations::DFE_CLUSTER == $instance->guest_location_nbr &&
                'start' == $_buttonName &&
                ProvisionStates::PROVISIONED == $instance->state_nbr
            ) {
                $_href = $this->buildInstanceUrl($instance->instance_name_text);

                $_button['text'] = 'Launch!';
                $_disabled = $_disabledClass = null;
                $_buttonName = 'launch';
            } else {
                $_href = isset($_button['href']) ? $_button['href'] : '#';
            }

            $_html .= <<<HTML
  <a id="dspcontrol___{$_buttonName}___{$instance->instance_name_text}" class="btn btn-xs btn-{$_button['color']} {$_disabledClass} col-xs-2 col-sm-2" {$_disabled} href="{$_href}" {$_hint}><i class="fa fa-{$_button['icon']}"></i><span class="hidden-sm hidden-xs"> {$_button['text']}</span></a>
HTML;
        }

        $_gettingStartedButton =
            '<a class="btn btn-xs btn-info col-xs-2 col-sm-2 dsp-help-button" id="dspcontrol-' .
            $instance->instance_name_text .
            '" data-placement="middle" title="Help" target="_blank" href="' .
            config('dashboard.help-button-url') .
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
    public function getStatusIcon($status, $key = false)
    {
        $_spinner = config('icons.spinner');
        $_message = null;
        $_running = false;

        switch ($status->state_nbr) {
            default:
                $_statusIcon = $_icon = $_spinner;
                $_message =
                    'Your request is being processed.';
                break;

            case ProvisionStates::CREATED:
            case ProvisionStates::PROVISIONING:
                $_statusIcon = $_icon = $_spinner;
                $_message =
                    'Your instance is being created, with lots of love! You will receive an email when it is ready.';
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
                $_statusIcon = $_icon = config('icons.dead');
                break;

            case ProvisionStates::PROVISIONED:
                $_message = 'Your instance is up and running.';
                $_running = true;
                $_statusIcon = $_icon = config('icons.up');
                break;

            case ProvisionStates::DEPROVISIONED:
                $_statusIcon = $_icon = config('icons.dead');;
                $_message = 'This DSP is terminated. All you can do is destroy it.';
                break;
        }

        return [$_icon, $_statusIcon, $_message, $_running];
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
    protected function _apiCall($url, $payload = [], $returnAll = true, $method = Request::METHOD_POST)
    {
        /** @type OpsClientService $_service */
        $_service = app(OpsClientServiceProvider::IOC_NAME);
        $_response = $_service->any($url, $payload, [], $method);

        if ($_response && is_object($_response) && isset($_response->success)) {
            return $returnAll ? $_response : $_response->response;
        }

        //	Error and redirect
        \Session::flash(
            'dashboard-failure',
            'An unexpected situation has occurred with your request. Please try again in a few minutes, or email <a href="mailto:support@dreamfactory.com">support@dreamfactory.com</a>.'
        );

        if (is_string($_response)) {
            \Log::error('Console API call received unexpected result: ' . $_response);
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
    public function createDivId($prefix, $id, $name)
    {
        return implode('___', [$prefix, $id, $name]);
    }

    /**
     * Get a hashed id suitable for framing
     *
     * @param string $valueToHash
     *
     * @return string
     */
    public function hashId($valueToHash)
    {
        if (empty($valueToHash)) {
            return null;
        }

        return hash(DashboardDefaults::SIGNATURE_METHOD, config('app.key') . $valueToHash);
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
     * Sets default domain and ensures leading period
     *
     * @param string $defaultDomain
     *
     * @return string
     */
    protected function setDefaultDomain($defaultDomain)
    {
        return $this->_defaultDomain = '.' . trim($defaultDomain, '. ');
    }

    /**
     * @return boolean
     */
    public function isEnableCaptcha()
    {
        return $this->_requireCaptcha;
    }

    /**
     * @return OpsClientService
     */
    protected function _getOpsClient()
    {
        return $this->app[OpsClientServiceProvider::IOC_NAME]
            ?: function () {
                throw new \RuntimeException(
                    'DFE Console services are not available.'
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
    protected function _ensureServer($serverId, $expectedType = null, $onlyId = true)
    {
        if (!empty($serverId)) {
            $_server = $this->_findServer($serverId);

            if ($expectedType && $_server->server_type_id != $expectedType) {
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

        if (!$this->_useConfigServers) {
            //  Return an empty array allowing the console to decide where to place the instance
            return $_config;
        }

        //  Check for a cluster override
        $_clusterId = config('dashboard.override-cluster-id');

        if (!empty($_clusterId)) {
            if (false === ($_server = $this->_findCluster($_clusterId))) {
                return false;
            }

            //  If you pick a cluster, you get no more choices
            $_config['cluster-id'] = $_server->id;

            return $_config;
        }

        //  Check cluster server overrides
        $_dbServerId = config('dashboard.override-db-server-id');

        if (false === ($_serverId = $this->_ensureServer($_dbServerId, ServerTypes::DB, true))) {
            return false;
        } else if ($_serverId) {
            $_config['db-server-id'] = $_serverId;
        }

        $_appServerId = config('dashboard.override-app-server-id');

        if (false === ($_serverId = $this->_ensureServer($_appServerId, ServerTypes::APP, true))) {
            return false;
        } else if ($_serverId) {
            $_config['app-server-id'] = $_serverId;
        }

        $_webServerId = config('dashboard.override-web-server-id');

        if (false === ($_serverId = $this->_ensureServer($_webServerId, ServerTypes::WEB, true))) {
            return false;
        } else if ($_serverId) {
            $_config['web-server-id'] = $_serverId;
        }

        return $_config;
    }

    /**
     * Renders an instance view
     *
     * @param array|\stdClass|Instance $instance
     * @param array                    $data      Any data needed to build the table
     * @param string                   $panelType The type panel. Can be "default", "create", or "import"
     *
     * @return string
     */
    public function renderInstance($instance, $data = [], $panelType = 'default')
    {
        return $this->_buildInstancePanel($instance, $data, $panelType, true);
    }

    /**
     * Renders multiple instance views
     *
     * @param array  $instances
     * @param array  $data      Any data needed to build the table
     * @param string $panelType The type panel. Can be "default", "create", or "import"
     * @param bool   $asArray   If true, the instances are returned rendered into an array. If false, a single string is returned
     *
     * @return array|string
     */
    public function renderInstances($instances = [], $data = [], $panelType = 'default', $asArray = true)
    {
        $_rendered = [];

        foreach ($instances as $_instance) {
            $_rendered[] = $this->renderInstance($_instance, $data, $panelType);
        }

        return $asArray ? $_rendered : implode(PHP_EOL, $_rendered);
    }

    /**
     * Based on the configured number of instances per row, set the appropriate grid classes.
     */
    protected function _determineGridLayout()
    {
        $this->_panelsPerRow = config('panels.panels-per-row', DashboardDefaults::PANELS_PER_ROW);

        if ($this->_panelsPerRow < 1) {
            $this->_panelsPerRow = 1;
        } else if ($this->_panelsPerRow > 6) {
            $this->_panelsPerRow = 6;
        }

        switch ($this->_panelsPerRow) {
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
    protected function _getPanelIcons($status)
    {
        $_message = null;
        $_spinner = config('icons.spinner', DashboardDefaults::SPINNING_ICON);

        switch ($status->state_nbr) {
            case ProvisionStates::CREATION_ERROR:
            case ProvisionStates::PROVISIONING_ERROR:
            case ProvisionStates::DEPROVISIONING_ERROR:
                $_icon = config('icons.dead');
                $_message = \Lang::get('dashboard.status-error');
                break;

            case ProvisionStates::CREATED:
            case ProvisionStates::PROVISIONING:
                $_icon = $_spinner;
                $_message = \Lang::get('dashboard.status-starting');
                break;

            case ProvisionStates::DEPROVISIONING:
                $_icon = $_spinner;
                $_message = \Lang::get('dashboard.status-stopping');
                break;

            case ProvisionStates::PROVISIONED:
                $_icon = config('icons.up');
                $_message = \Lang::get('dashboard.status-up');
                break;

            case ProvisionStates::DEPROVISIONED:
                $_icon = config('icons.dead');;
                $_message = \Lang::get('dashboard.status-dead');
                break;

            default:
                $_icon = $_spinner;
                $_message = \Lang::get('dashboard.status-other');
                break;
        }

        return ['icon' => $_icon, 'message' => $_message];
    }

    /**
     * @param \stdClass|Instance $instance
     *
     * @return array
     */
    protected function _getPanelButtons($instance)
    {
        $_buttons = [
            'launch' => [
                'context' => 'btn-success',
                'icon'    => 'fa-play',
                'hint'    => 'Launch your instance',
                'text'    => 'Launch',
            ],
            //            'stop'   => ['context' => 'btn-warning', 'icon' => 'fa-stop', 'hint' => 'Stop your instance', 'text' => 'Stop'],
            'export' => [
                'context' => 'btn-info',
                'icon'    => 'fa-cloud-download',
                'hint'    => 'Create an export of your instance',
                'text'    => 'Export',
            ],
            //            'import' => [
            //                'context' => 'btn-warning',
            //                'icon'    => 'fa-cloud-upload',
            //                'hint'    => 'Import a prior export',
            //                'text'    => 'Import',
            //            ],
            'delete' => [
                'context' => 'btn-danger',
                'icon'    => 'fa-times',
                'hint'    => 'Permanently destroy this instance',
                'text'    => 'Destroy',
            ],
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

    protected function _makeToolbarButton($id, $text, array $options = [])
    {
        static $_template = [
            'type'    => 'button',
            'size'    => 'btn-xs',
            'context' => 'btn-info',
            'icon'    => '',
            'hint'    => '',
            'data'    => [],
        ];

        if (isset($options['icon'])) {
            $options['icon'] = '<i class="fa fa-fw ' . $options['icon'] . ' instance-toolbar-button"></i>';
        }

        if (!isset($options['hint'])) {
            $options['hint'] = $text . ' instance';
        }

        $_action = str_replace(['_', ' '], '-', trim(strtolower($text)));

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
    protected function _getToolbarButtons($instance)
    {
        $_id = $instance->instance_id_text;

        if (GuestLocations::DFE_CLUSTER == $instance->guest_location_nbr) {
            $_buttons = [
                'launch' => $this->_makeToolbarButton($_id,
                    'Launch',
                    ['context' => 'btn-success', 'icon' => 'fa-play']),
                'delete' => $this->_makeToolbarButton($_id,
                    'Delete',
                    ['context' => 'btn-danger', 'icon' => 'fa-times']),
                'export' => $this->_makeToolbarButton($_id,
                    'Export',
                    ['context' => 'btn-info', 'icon' => 'fa-cloud-download']),
            ];
        } else {
            //@todo make dynamic call to provisioner to find out supported operations
            $_buttons = [
                'start'     => $this->_makeToolbarButton($_id,
                    'Start',
                    ['context' => 'btn-success', 'icon' => 'fa-play',]),
                'stop'      => $this->_makeToolbarButton($_id,
                    'Stop',
                    ['context' => 'btn-warning', 'icon' => 'fa-stop',]),
                'terminate' => $this->_makeToolbarButton($_id,
                    'Terminate',
                    ['context' => 'btn-danger', 'icon' => 'fa-times',]),
                'export'    => $this->_makeToolbarButton($_id,
                    'Export',
                    ['context' => 'btn-info', 'icon' => 'fa-cloud-download',]),
            ];
        }

        return $_buttons;
    }

    /**
     * @param string $panelType
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function panelConfig($panelType, $key, $default = null)
    {
        static $_config = [];

        if (empty($_config)) {
            $_config = config('panels');
        }

        return array_get($_config, $panelType . '.' . $key, $default);
    }

    /**
     * @param string $panelType The panel to render
     * @param array  $data      Any additional view data
     * @param bool   $render    If true, view is rendered and html is returned
     *
     * @return View|string
     */
    public function renderPanel($panelType, $data = [], $render = true)
    {
        if (!PanelTypes::contains($panelType = $panelType ?: DashboardDefaults::DEFAULT_PANEL)) {
            throw new \InvalidArgumentException('The panel type "' . $panelType . '" is invalid.');
        }

        $_blade = $this->panelConfig($panelType, 'template', DashboardDefaults::SINGLE_INSTANCE_BLADE);

        $_offeringsHtml = null;

        $_dudes = Dashboard::getProvisioners();

        if (!is_object($_dudes)) {
            throw new \RuntimeException('Invalid response from the console.');
        }

        if ($_dudes->success) {
            foreach ($_dudes->response as $_host) {
                if ($_host->id == 'rave') {
                    $_offeringsHtml = $this->_buildOfferingsInput($_host);
                    break;
                }
            }
        }

        $_description = \Lang::get($this->panelConfig($panelType, 'description'));

        if (empty($_description)) {
            $_description = null;
        }

        if (PanelTypes::SINGLE == $panelType) {
            $data['panelSize'] = array_get($data, 'panelSize', $this->_columnClass);
        }

        $_view = view(
            $_blade,
            array_merge(
                $data,
                [
                    'formId'           => 'form-' . $panelType,
                    'panelDescription' => $_description,
                    'offerings'        => $_offeringsHtml,
                    'panelTitle'       => \Lang::get('dashboard.instance-' . $panelType . '-title'),
                    'panelType'        => $panelType,
                    'panelContext'     => $this->panelConfig($panelType, 'context'),
                    'headerIcon'       => $this->panelConfig($panelType, 'header-icon'),
                ]
            )
        );

        return $render ? $_view->render() : $_view;
    }

    /**
     * @param \stdClass $host
     *
     * @return string|null
     */
    protected function _buildOfferingsInput($host)
    {
        if (!isset($host->offerings)) {
            return null;
        }

        $_html = null;

        if (!empty($host->offerings)) {
            foreach ($host->offerings as $_tag => $_offering) {
                $_data = (array)$_offering;
                $_displayName = array_get($_data, 'name', $_tag);
                $_items = array_get($_data, 'items', []);
                $_suggested = array_get($_data, 'suggested');

                $_helpBlock =
                    (null !== ($_helpBlock = array_get($_data, 'help-block')))
                        ? '<p class="help-block">' . $_helpBlock . '</p>'
                        : null;

                if (!empty($_items)) {
                    $_options = null;

                    foreach ($_items as $_name => $_config) {
                        $_attributes = $_html = $_selected = null;

                        $_config = (array)$_config;

                        if (null === ($_description = array_get($_config, 'description'))) {
                            $_description = $_name;
                        } else {
                            unset($_config['description']);
                        }

                        foreach ($_config as $_key => $_value) {
                            $_key = str_replace(['"', '\'', '_', ' ', '.', ','], '-', strtolower($_key));
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

                    $_html .= view('layouts.partials.offerings',
                        [
                            'tag'         => $_tag,
                            'displayName' => $_displayName,
                            'options'     => $_options,
                            'helpBlock'   => $_helpBlock,
                        ])->render();
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
    protected function _buildInstanceLink($status)
    {
        return
            '<a href="https://' . $this->getDefaultDomain($status->instance_name_text) . '" ' .
            'target="_blank" class="dsp-launch-link">' . $status->instance_name_text . '</a>';
    }

    /**
     * @param \stdClass|Instance $instance
     * @param array              $data
     * @param string             $panelType The panel to use if not "default"
     * @param bool               $rendered
     *
     * @return \Illuminate\View\View|string
     */
    protected function _buildInstancePanel($instance, $data = [], $panelType = 'default', $rendered = false)
    {
        $_viewData = $this->buildInstancePanelData($instance, $data, $panelType);
        $_viewName = $this->panelConfig($panelType, 'template', DashboardDefaults::DEFAULT_INSTANCE_BLADE);
        $_view = \View::make($_viewName, $_viewData, ['instance' => $instance]);

        return $rendered ? $_view->render() : $_view;
    }

    /**
     * Returns a panel override section
     *
     * @param string $panelType
     *
     * @return mixed
     */
    protected function _getPanelOverrides($panelType = PanelTypes::SINGLE)
    {
        static $_panels = [];

        if (empty($_panels)) {
            foreach (PanelTypes::getDefinedConstants() as $_index => $_panelType) {
                $_panels[$_panelType] = [
                    'headerIcon'                => $this->panelConfig($_panelType, 'header-icon'),
                    'headerIconSize'            => $this->panelConfig($_panelType, 'header-icon-size', 'fa-1x'),
                    'headerStatusIcon'          => $this->panelConfig($_panelType, 'header-status-icon'),
                    'headerStatusIconSize'      => $this->panelConfig($_panelType, 'header-status-icon-size'),
                    'instanceStatusIcon'        => $this->panelConfig($_panelType, 'status-icon'),
                    'instanceStatusIconSize'    => $this->panelConfig($_panelType, 'status-icon-size'),
                    'instanceStatusIconContext' => $this->panelConfig($_panelType, 'status-icon-context'),
                    'panelContext'              => $this->panelConfig($_panelType, 'context', 'panel-info'),
                ];

                $_key = $this->panelConfig($panelType, 'description');

                if ($_key != ($_panelDescription = \Lang::get($_key))) {
                    $_panels[$_panelType]['panelDescription'] = $_panelDescription;
                }
            }
        }

        return $_panels[$panelType];
    }

    /**
     * @param \DreamFactory\Enterprise\Dashboard\Things\InstancePanel $panel
     *
     * @return int
     */
    public function push(InstancePanel $panel)
    {
        return array_push($this->_panels, $panel);
    }

    /**
     * @return InstancePanel|null
     */
    public function pop()
    {
        return array_pop($this->_panels);
    }

    /**
     * @param array $mergeData
     *
     * @return null|string
     */
    public function renderStack($mergeData = [])
    {
        $_html = null;

        foreach ($this->_panels as $_panel) {
            $_html .= $_panel->renderPanel($mergeData);
        }

        return $_html;
    }

    /**
     * Constructs and returns the fully qualified host name of an instance
     *
     * @param string $instanceName
     *
     * @return string
     */
    protected function buildInstanceUrl($instanceName)
    {
        return
            config('dashboard.default-domain-protocol',
                'https') . '://' . $instanceName . $this->getDefaultDomain();
    }
}
