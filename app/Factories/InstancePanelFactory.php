<?php namespace DreamFactory\Enterprise\Dashboard\Factories;

use DreamFactory\Enterprise\Common\Contracts\StaticRenderFactory;
use DreamFactory\Enterprise\Dashboard\Enums\DashboardDefaults;

/**
 * A single instance panel on the dashboard
 */
class InstancePanelFactory implements StaticRenderFactory
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type int The number of panels across. Used to determine proper width and spacing
     */
    protected $_columnsPerPanel = DashboardDefaults::COLUMNS_PER_PANEL;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public static function make( $panelId, $viewData = [] )
    {
        $_viewData = static::_preparePanelData( $viewData );
        $_view = \View::make( \Config::get( 'instance-panel-template', DashboardDefaults::SINGLE_INSTANCE_BLADE ), $_viewData );

        return $_view;
    }

    /** @inheritdoc */
    public static function render( $panelId, $viewData = [], \Closure $callback = null )
    {
        return static::make( $panelId, $viewData )->render( $callback );
    }

    /**
     * Provides the array of data necessary to populate an individual instance panel
     *
     * @param array $viewData
     *
     * @return array
     */
    protected function _preparePanelData( $viewData = [] )
    {
        return array_merge(
            [
                'panelSize'              => $this->_columnClass,
                'panelContext'           => config( 'dashboard.panel-context', 'panel-info' ),
                'instanceName'           => $this->_id,
                'defaultDomain'          => '.' .
                    trim( config( 'dashboard.default-dns-zone' ), '.' ) . '.' .
                    trim( config( 'dashboard.default-dns-domain' ), '.' ),
                'headerIconSize'         => 'fa-1x',
                'instanceDivId'          => $this->createDivId( 'instance', $instance ),
                'instanceStatusIconSize' => 'fa-3x',
                'instanceUrl'            => config( 'dashboard.default-domain-protocol', 'https' ) .
                    '://' .
                    $instance->instance_name_text .
                    $this->_defaultDomain,
                'panelButtons'           => $this->_getPanelButtons( $instance ),
            ],
            $this->_getInstanceStatus( $instance )
        );
    }

    /**
     * @param \stdClass|Instance $instance
     *
     * @return array
     */
    protected
    function _getInstanceStatus( $instance )
    {
        $_spinner = config( 'dashboard.icons.spinner', DashboardDefaults::SPINNING_ICON );

        switch ( $instance->state_nbr )
        {
            case ProvisionStates::CREATED:
                $_icon = $_spinner;
                $_context = 'btn-success';
                $_text = \Lang::get( 'dashboard.status-started' );
                break;

            case ProvisionStates::PROVISIONING:
                $_icon = $_spinner;
                $_context = 'btn-info';
                $_text = \Lang::get( 'dashboard.status-started' );
                break;

            case ProvisionStates::PROVISIONED:
                $_icon = config( 'dashboard.icons.up' );
                $_context = 'btn-success';
                $_text = \Lang::get( 'dashboard.status-up' );
                break;

            case ProvisionStates::DEPROVISIONING:
                $_icon = $_spinner;
                $_context = 'btn-info';
                $_text = \Lang::get( 'dashboard.status-stopping' );
                break;

            case ProvisionStates::DEPROVISIONED:
                $_icon = config( 'dashboard.icons.instance-terminating' );
                $_context = 'btn-warning';
                $_text = \Lang::get( 'dashboard.status-terminating' );
                break;

            case ProvisionStates::PROVISIONING_ERROR:
            case ProvisionStates::DEPROVISIONING_ERROR:
            case ProvisionStates::CREATION_ERROR:
                $_icon = config( 'dashboard.icons.instance-dead' );
                $_context = 'btn-danger';
                $_text = \Lang::get( 'dashboard.status-dead' );
                break;

            default:
                $_icon = config( 'dashboard.icons.instance-unknown' );
                $_context = 'btn-warning';
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
     * @param \stdClass|Instance $instance
     *
     * @return array
     */
    protected function _getPanelButtons( $instance )
    {
        $_buttons = [
            'launch' => ['id' => '', 'size' => '', 'context' => 'btn-success', 'icon' => 'fa-play', 'hint' => '', 'text' => 'Launch'],
            'stop'   => ['id' => '', 'size' => '', 'context' => 'btn-warning', 'icon' => 'fa-stop', 'hint' => '', 'text' => 'Stop'],
            'import' => ['id' => '', 'size' => '', 'context' => 'btn-warning', 'icon' => 'fa-cloud-upload', 'hint' => '', 'text' => 'Import'],
            'export' => ['id' => '', 'size' => '', 'context' => 'btn-info', 'icon' => 'fa-cloud-download', 'hint' => '', 'text' => 'Export'],
            'delete' => ['id' => '', 'size' => '', 'context' => 'btn-danger', 'icon' => 'fa-times', 'hint' => '', 'text' => 'Destroy'],
            'help'   => [
                'id'      => 'instance-control-' . $instance->instance_name_text,
                'size'    => '',
                'context' => 'btn-danger',
                'icon'    => 'fa-times',
                'hint'    => '',
                'text'    => 'Destroy'
            ],
        ];

        return $_buttons;
    }
}
