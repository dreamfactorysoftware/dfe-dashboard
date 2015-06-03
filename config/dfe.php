<?php
//******************************************************************************
//* Master DFE Settings
//******************************************************************************

use DreamFactory\Enterprise\Common\Enums\EnterpriseDefaults;

return [
    //******************************************************************************
    //* General
    //******************************************************************************
    //  The id of THIS cluster
    'cluster-id'        => env( 'DFE_CLUSTER_ID' ),
    //  A string to be pre-pended to instance names for non-admin users
    'instance-prefix'   => env( 'DFE_DEFAULT_INSTANCE_PREFIX' ),
    'signature-method'  => env( 'DFE_SIGNATURE_METHOD', EnterpriseDefaults::DEFAULT_SIGNATURE_METHOD ),
    //  If true, users may self-register. Otherwise, admins must create users */
    'open-registration' => true,
];
