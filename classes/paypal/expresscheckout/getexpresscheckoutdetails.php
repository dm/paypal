<?php
class PayPal_ExpressCheckout_GetExpressCheckoutDetails extends PayPal_ExpressCheckout {
    
    protected function request_rules() {
        return array(
            'token'
            
        );
        
    }

    protected function response_rules() {
        
    }
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
