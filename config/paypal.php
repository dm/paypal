<?php

defined('SYSPATH') or die('No direct script access.');

return array(
// PayPal environment: live, sandbox, beta-sandbox
    'environment' => 'sandbox',
    // Language for responses
    'lang' => 'en_US',
    'curl_options' => array(
        CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_SSL_VERIFYHOST => FALSE,
        CURLOPT_USERAGENT => "Kohana",
    ),
    'sandbox' => array(
// PayPal API and username
        'username' => NULL,
        'password' => NULL,
        // PayPal API signature
        'signature' => NULL,
        // Static api id for sandbox apps.
        'api_id' => 'APP-80W284485P519543T',
    ),
    'live' => array(
// PayPal API and username
        'username' => NULL,
        'password' => NULL,
        // PayPal API signature
        'signature' => NULL,
        'api_id' => NULL,
    )
);
