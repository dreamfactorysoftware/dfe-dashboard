<?php namespace DreamFactory\Enterprise\Dashboard\Things;

use DreamFactory\Enterprise\Dashboard\Enums\DashboardDefaults;
use DreamFactory\Enterprise\Database\Enums\ProvisionStates;
use DreamFactory\Enterprise\Database\Models\Instance;

/**
 * An object that represents a single instance panel/thumbnail on the dashboard
 */
class InstancePanel
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string The id of this panel
     */
    protected $_id;
    /**
     * @type array The data used to build the panel view
     */
    protected $_data = [];
    /**
     * @type string The name of the blade to use for rendering the panel
     */
    protected $_blade = DashboardDefaults::SINGLE_INSTANCE_BLADE;
    /**
     * @type string
     */
    protected $_defaultDomain;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string $id    The id of the panel
     * @param array  $data  An array of data used to render the panel
     * @param string $blade The name of the blade to use for rendering. If null, the dashboard.instance-panel-template config value is used.
     */
    public function __construct($id, $data = [], $blade = null)
    {
        $this->_id = $id;
        $this->_data = $data ?: [];
        $this->_blade = $blade ?: config('dfe.panels.default.template', DashboardDefaults::SINGLE_INSTANCE_BLADE);

        $this->_defaultDomain =
            '.' . trim(config('dfe.dashboard.default-dns-zone'),
                '.') . '.' . trim(config('dfe.dashboard.default-dns-domain'), '.');
    }

    /**
     * @param array $mergeData An array of data merged with the object's data for rendering
     *
     * @return \Illuminate\View\View
     */
    public function buildPanel($mergeData = [])
    {
        return
            \View::make($this->_blade, $this->_data, $mergeData);
    }

    /**
     * @param array    $mergeData
     * @param \Closure $callback
     *
     * @return string
     */
    public function renderPanel($mergeData = [], \Closure $callback = null)
    {
        return $this->buildPanel($mergeData)->render($callback);
    }

    /**
     * @param \stdClass|Instance $instance
     *
     * @return array
     */
    protected function _prepareViewData($instance)
    {
        return array_merge(
            [
                'panelContext'           => config('dfe.dashboard.panel-context', 'panel-info'),
                'instanceName'           => $this->_id,
                'defaultDomain'          => $this->_defaultDomain,
                'headerIconSize'         => 'fa-1x',
                'instanceDivId'          => $this->_htmlId('instance', $instance->instance_name_text),
                'instanceStatusIconSize' => 'fa-3x',
                'instanceUrl'            => config('dfe.dashboard.default-domain-protocol', 'https') .
                    '://' .
                    $instance->instance_name_text .
                    $this->_defaultDomain,
                'panelButtons'           => $this->_getPanelButtons($instance),
            ],
            $this->_getStatusData($instance)
        );
    }

    /**
     * Build a parseable string to use for HTML ids
     *
     * @param string $prefix
     * @param string $name
     * @param string $delimiter
     *
     * @return string
     */
    protected function _htmlId($prefix, $name, $delimiter = '___')
    {
        return implode($delimiter, [$prefix, $this->_id, $name]);
    }

    /**
     * @param \stdClass|Instance $instance
     *
     * @return array
     */
    protected function _getStatusData($instance)
    {
        $_spinner = config('dfe.icons.spinner', DashboardDefaults::SPINNING_ICON);

        switch ($instance->state_nbr) {
            case ProvisionStates::CREATED:
                $_icon = $_spinner;
                $_context = 'btn-success';
                $_text = \Lang::get('dashboard.status-started');
                break;

            case ProvisionStates::PROVISIONING:
                $_icon = $_spinner;
                $_context = 'btn-info';
                $_text = \Lang::get('dashboard.status-started');
                break;

            case ProvisionStates::PROVISIONED:
                $_icon = config('dfe.icons.up');
                $_context = 'btn-success';
                $_text = \Lang::get('dashboard.status-up');
                break;

            case ProvisionStates::DEPROVISIONING:
                $_icon = $_spinner;
                $_context = 'btn-info';
                $_text = \Lang::get('dashboard.status-stopping');
                break;

            case ProvisionStates::DEPROVISIONED:
                $_icon = config('dfe.icons.instance-terminating');
                $_context = 'btn-warning';
                $_text = \Lang::get('dashboard.status-terminating');
                break;

            case ProvisionStates::PROVISIONING_ERROR:
            case ProvisionStates::DEPROVISIONING_ERROR:
            case ProvisionStates::CREATION_ERROR:
                $_icon = config('dfe.icons.instance-dead');
                $_context = 'btn-danger';
                $_text = \Lang::get('dashboard.status-dead');
                break;

            default:
                $_icon = config('dfe.icons.instance-unknown');
                $_context = 'btn-warning';
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
     * @param \stdClass|Instance $instance
     *
     * @return array
     */
    protected function _getPanelButtons($instance)
    {
        $_buttons = [
            'launch' => [
                'id'      => '',
                'size'    => '',
                'context' => 'btn-success',
                'icon'    => 'fa-play',
                'hint'    => '',
                'text'    => 'Launch',
            ],
            'stop'   => [
                'id'      => '',
                'size'    => '',
                'context' => 'btn-warning',
                'icon'    => 'fa-stop',
                'hint'    => '',
                'text'    => 'Stop',
            ],
            'import' => [
                'id'      => '',
                'size'    => '',
                'context' => 'btn-warning',
                'icon'    => 'fa-cloud-upload',
                'hint'    => '',
                'text'    => 'Import',
            ],
            'export' => [
                'id'      => '',
                'size'    => '',
                'context' => 'btn-info',
                'icon'    => 'fa-cloud-download',
                'hint'    => '',
                'text'    => 'Export',
            ],
            'delete' => [
                'id'      => '',
                'size'    => '',
                'context' => 'btn-danger',
                'icon'    => 'fa-times',
                'hint'    => '',
                'text'    => 'Destroy',
            ],
            'help'   => [
                'id'      => 'instance-control-' . $this->_id,
                'size'    => '',
                'context' => 'btn-danger',
                'icon'    => 'fa-times',
                'hint'    => '',
                'text'    => 'Destroy',
            ],
        ];

        return $_buttons;
    }
}