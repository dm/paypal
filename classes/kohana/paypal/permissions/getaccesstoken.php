<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * PayPal ExpressCheckout integration.
 *
 * @see  https://cms.paypal.com/ca/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_PermissionsGetAccessTokenAPI
 *
 * @package    Kohana
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Kohana_PayPal_Permissions_GetAccessToken extends PayPal_Permissions {

    /**
     * Need a token and the verifier
     * @return type
     */
    protected function rules() {
        return array(
            'token' => array(
                array('not_empty')
            ),
            'verifier' => array(
                array('not_empty')
            )
        );
    }

    protected function response_rules() {
        return array(
            'token' => array(
                array('not_empty')
            ),
            'tokenSecret' => array(
                array('not_empty')
            ),
        );
    }

}

// End PayPal_ExpressCheckout
