<?php namespace DreamFactory\Enterprise\Services\Auditing\Enums;

use DreamFactory\Library\Utility\Enums\FactoryEnum;

/**
 * The type of audit message formats available
 */
class AuditMessageFormats extends FactoryEnum
{
    //*************************************************************************
    //* Constants
    //*************************************************************************

    /**
     * @var int GELF v1.1
     */
    const GELF = 0;
}
