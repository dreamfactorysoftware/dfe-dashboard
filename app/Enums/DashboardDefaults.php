<?php namespace DreamFactory\Enterprise\Dashboard\Enums;

use DreamFactory\Library\Utility\Enums\FactoryEnum;

class DashboardDefaults extends FactoryEnum
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string
     */
    const SPINNING_ICON = 'fa fa-fw fa-spinner fa-spin';
    /**
     * @type int The number of grid columns each instance panel utilizes. Each row consists of 12 utilizable columns. This setting can be from 3 to
     *       12. 3 being the smallest (four instances per row), and 12 the largest (one instance per row)
     */
    const COLUMNS_PER_PANEL = 3;
    /**
     * @type int The number of panels to display in each row. This setting can be 1, 2, 3, 4, and 6. Five panels per row aren't available.
     */
    const PANELS_PER_ROW = 4;
    /**
     * @type string
     */
    const CREATE_INSTANCE_BLADE = 'layouts.partials.create-instance';
    /**
     * @type string
     */
    const IMPORT_INSTANCE_BLADE = 'layouts.partials.import-instance';
    /**
     * @type string
     */
    const SINGLE_INSTANCE_BLADE = 'layouts.partials.single-instance';
    /**
     * @type string
     */
    const DEFAULT_PANEL = 'default';
    /**
     * @type string
     */
    const INSTANCE_PREFIX = 'dsp-';
    /**
     * @type string
     */
    const PANEL_CONTEXT = 'panel-info';
    /**
     * @type string
     */
    const PANEL_CREATE_TITLE = 'Create a New Instance';

}