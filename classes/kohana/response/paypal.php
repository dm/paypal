<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * PayPal response. It has all the flexibility of Kohana validations.
 * 
 * This object is immutable.
 * 
 * @see Validation
 * 
 * @package PayPal
 * @category Response
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2012, Hète.ca Inc.
 */
abstract class Kohana_Response_PayPal extends Validation implements PayPal_Constants {

    /**
     * Defines all the possible acknowledgements values.
     * 
     * @var array 
     */
    public static $ACKNOWLEDGEMENTS = array(
        static::SUCCESS,
        static::FAILURE,
        static::SUCCESS_WITH_WARNING,
        static::FAILURE_WITH_WARNING
    );

    /**
     * Defines all the success acknowledgements
     * 
     * @var array 
     */
    public static $SUCCESS_ACKNOWLEDGEMENTS = array(
        static::SUCCESS,
        static::SUCCESS_WITH_WARNING
    );

    /**
     * Defines all the failure acknowledgements.
     * 
     * @var array 
     */
    public static $FAILURE_ACKNOWLEDGEMENTS = array(
        static::FAILURE,
        static::FAILURE_WITH_WARNING
    );

    /**
     * Redirection url for this request.
     * @var string 
     */
    public $redirect_url;

    /**
     * Original response.
     * @var Response 
     */
    public $response;

    /**
     * 
     * @param Response $response from a PayPal request.
     */
    public function __construct(Response $response = NULL, array $data = NULL) {

        parent::__construct($data);

        $this->response = $response;
        // Adding default rules
        $this->rule("responseEnvelope_ack", "not_empty");
        $this->rule("responseEnvelope_ack", "PayPal_Valid::contained", array(":value", static::$SUCCESS_ACKNOWLEDGEMENTS));
    }

    public function data($key = NULL) {
        return $key === NULL ? parent::data() : $this[$key];
    }

}

?>
