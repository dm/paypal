<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Abstract PayPal integration.
 *
 * @link  https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/library_documentation
 *
 * @package    Kohana
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
abstract class PayPal {

    /**
     * Valide de façon récursive un tableau associatif avec un tableau de clés.
     * @param type $data
     * @param type $req
     * @return boolean
     */
    private static function check_required_array_key_recursive($data, $req) {
        // no more required fields
        if (empty($req))
            return true;

        // no more data fields; obviously lacks required field(s)
        if (empty($data))
            return false;


        foreach ($req as $name => $subtree) {
            // unnamed; it's a list
            if (is_numeric($name)) {
                foreach ($data as $dataitem) {
                    if (PayPal::check_required_array_key_recursive($dataitem, $subtree) == false)
                        return false;
                }
            } else {
                // required field doesn't exist
                if (!isset($data[$name]))
                    return false;

                // fine so far; down we go
                if (!empty($subtree)
                        && PayPal::check_required_array_key_recursive($data[$name], $subtree) == false
                ) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 
     * @param string $class 
     * @param array $params
     * @return \class
     */
    public static function factory($class, array $params = array()) {
        $class = "PayPal_" . $class;
        return new $class($params);
    }

    // Environment type
    /**
     *
     * @var type 
     */
    protected $_environment;

    /**
     * Basic params values.
     * @var type 
     */
    private $_params = array();

    /**
     * 
     * @var type 
     */
    private $_headers = array();

    /**
     * Returns the redirection command.
     * @var type 
     */
    protected abstract function redirect_command();

    /**
     * Returns pre-computed redirection parameters based on request results.
     */
    protected abstract function redirect_param($results);

    /**
     * Key tree of required values.
     * @return type
     */
    protected abstract function required();

    /**
     * PayPal method name based on the class name.
     * @var string 
     */
    private function method() {

        $method = str_replace("PayPal_", "", get_class($this));

        return implode("/", explode("_", $method));
    }

    /**
     * Creates a new PayPal instance for the given username, password,
     * and signature for the given environment.
     *
     * @param   string  API username
     * @param   string  API password
     * @param   string  API signature
     * @param   string  environment (one of: live, sandbox, sandbox-beta)
     * @return  void
     */
    public function __construct(array $params = array()) {

        $this->_environment = Kohana::$config->load("paypal.environment");

        $config = Kohana::$config->load('paypal.' . $this->_environment);

        $this->_headers = array(
            'X-PAYPAL-SECURITY-USERID' => $config['username'],
            'X-PAYPAL-SECURITY-PASSWORD' => $config['password'],
            'X-PAYPAL-SECURITY-SIGNATURE' => $config['signature'],
            'X-PAYPAL-REQUEST-DATA-FORMAT' => 'NV',
            'X-PAYPAL-RESPONSE-DATA-FORMAT' => 'NV',
            "X-PAYPAL-APPLICATION-ID" => Kohana::$config->load('paypal.app_id'),
        );

        $this->_params = $params;
    }

    /**
     * param() returns the param array, param($key) returns the value associated
     * to the key $key and param($key, $value) sets the $value at the specified
     * $key.
     * @param type $key
     * @param type $value
     * @return type
     */
    public function param($key = NULL, $value = NULL) {
        if ($key === NULL) {
            return $this->_params;
        } else if ($value === NULL) {
            return $this->_params[$key];
        } else {
            $this->_params[$key] = $value;
        }
    }

    /**
     * Validate the parameters of the PayPal request.
     */
    public function check() {
        return PayPal::check_required_array_key_recursive($this->param(), $this->required()) | true;
    }

    /**
     * Returns the NVP API URL for the current environment.
     *
     * @return  string
     */
    public function api_url() {
        if ($this->_environment === 'live') {
            // Live environment does not use a sub-domain
            $env = '';
        } else {
            // Use the environment sub-domain
            $env = $this->_environment . '.';
        }

        return 'https://svcs.' . $env . 'paypal.com/' . $this->method();
    }

    /**
     * Returns the redirect URL for the current environment.
     *
     * @see  https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_html_Appx_websitestandard_htmlvariables#id08A6HF00TZS
     *
     * @param   string   PayPal command
     * @param   array    GET parameters
     * @return  string
     */
    public function redirect_url() {
        if ($this->_environment === 'live') {
            // Live environment does not use a sub-domain
            $env = '';
        } else {
            // Use the environment sub-domain
            $env = $this->_environment . '.';
        }

        // Add the command to the parameters
        $params = array('cmd' => '_' . $this->redirect_command) + $this->redirect_param();

        return 'https://www.' . $env . 'paypal.com/webscr?' . http_build_query($params, '', '&');
    }

    /**
     * Execute the PayPal POST request and returns the result.
     *
     * @see  https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_nvp_NVPAPIOverview
     *
     * @throws  Kohana_Exception
     * @param   string  method to call
     * @param   array   POST parameters
     * @return  array
     */
    public final function execute() {

        if (!$this->check()) {
            throw new PayPal_Exception("The param array does not validate the required keys.");
        }

        // Create POST data        
        $request = Request::factory($this->api_url())
                ->method(Request::POST)
                ->body(http_build_query($this->param()));

        foreach ($this->_headers as $key => $value) {
            $request->headers($key, $value);
        }

        // Setup the client
        $request->client()->options(CURLOPT_SSL_VERIFYPEER, FALSE)
                ->options(CURLOPT_SSL_VERIFYHOST, FALSE);


        try {
            // Get the Response for this Request
            $response = $request->execute();
        } catch (Request_Exception $e) {
            $code = $e->getCode();
            $error = $e->getMessage();

            throw new Kohana_Exception('PayPal API request for :method failed: :error (:code). :query',
                    array(':method' => $this->method(),
                        ':error' => $error,
                        ':code' => $code,
                        ':query' => wordwrap($request->body()),
            ));
        }

        // Parse the response
        parse_str($response->body(), $data);

        if (!isset($data['responseEnvelope_ack']) OR strpos($data['responseEnvelope_ack'], 'Success') === FALSE) {
            throw new PayPal_Exception($data);
        }

        return $data;
    }

}

// End PayPal
