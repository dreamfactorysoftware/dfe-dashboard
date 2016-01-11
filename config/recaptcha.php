<?php
//******************************************************************************
//* Settings for the reCAPTCHA doo-hickey
//******************************************************************************
return [
    'siteKey'   => getenv('RECAPTCHA_SITE_KEY') ?: '',
    'secretKey' => getenv('RECAPTCHA_SECRET_KEY') ?: '',
    'curl'      => true,
];
