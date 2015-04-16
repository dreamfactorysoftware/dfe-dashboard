<?php namespace DreamFactory\Enterprise\Dashboard\Enums;

use DreamFactory\Library\Utility\Enums\FactoryEnum;

class DashboardDefaults extends FactoryEnum
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type int The number of grid columns each instance panel utilizes. Each row consists of 12 utilizable columns. This setting can be from 3 to
     *       12. 3 being the smallest (four instances per row), and 12 the largest (one instance per row)
     */
    const COLUMNS_PER_PANEL = 3;
    /**
     * @var string
     */
    const INSTANCE_PANEL_TEMPLATE = 'layouts.partials._dashboard_instance-panel';
    /**
     * @var string
     */
    const SPINNING_ICON = 'fa fa-fw fa-spinner fa-spin';

}