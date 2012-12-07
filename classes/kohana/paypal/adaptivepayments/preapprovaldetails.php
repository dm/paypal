<?php

/**
 * 
 * @link https://cms.paypal.com/ca/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_APPreapprovalDetails
 * 
 * @package PayPal
 * @category AdaptativePayments
 * @author Quentin Avedissian <quentin.avedissian@gmail.com>
 * @copyright 
 */
class Kohana_PayPal_AdaptivePayments_PreapprovalDetails extends PayPal_AdaptivePayments {

    protected function rules() {

        return array(
            //PreapprovalDetailsRequest Fields
            'getBillingAddress' => array(
                array('PayPal_Valid::boolean')
            ),
            'preapprovalKey' => array(
                array('not_empty')
            ),
        );
    }

}

?>
