<?php namespace DreamFactory\Enterprise\Dashboard\Enums;

use DreamFactory\Library\Utility\Enums\FactoryEnum;

class PanelTypes extends FactoryEnum
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string
     */
    const SINGLE = 'default';
    /**
     * @type string
     */
    const CREATE = 'create';
    /**
     * @type string
     */
    const IMPORT = 'import';
}