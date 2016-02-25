<?php
/**
 * Beeminder_HttpDriver
 * 
 * Base class for integrating with the API using a HTTP client. Needs to be
 * extended to carry out the actual requests/ See Beeminder_HttpDriver_Curl for
 * a concrete example.
 * 
 * @package    Beeminder_Api
 * @author     Phil Newton <phil@sodaware.net>
 * @copyright  2012 Phil Newton <phil@sodaware.net>
 */


abstract class Beeminder_HttpDriver implements Beeminder_DriverInterface
{
    
    /**
     * Default options.
     * @var array
     */
    protected $_options = array(
        'protocol'   => 'https',
        'url'        => ':protocol://www.beeminder.com/api/v1/:path.:format',
        'format'     => 'json',
        'user_agent' => 'beeminder-php-api (https://github.com/beeminder/beeminder-php-api)',
        'http_port'  => 443,
        'timeout'    => 60,
        'username'   => null,
        'token'      => null
    );
    
    
    // ----------------------------------------------------------------------
    // -- Setting & Getting Options
    // ----------------------------------------------------------------------
    
    /**
     * Set an option.
     *
     * @param string $optionName The option to set.
     * @param mixed $optionValue The value to set.
     *
     * @return Beeminder_DriverInterface Current object instance.
     */
    public function setOption($optionName, $optionValue)
    {
        $this->_options[$optionName] = $optionValue;
        return $this;
    }
    
    /**
     * Get a single option value.
     * 
     * @param string $optionName Name of the option to retrieve.
     * @return mixed Option value.
     */
    public function getOption($optionName)
    {
        return $this->_options[$optionName];
    }
    
    /**
     * Get all options.
     * 
     * @return array of option key => value pairs..
     */
    public function getOptions()
    {
        return $this->_options;
    }
    
    
    // ----------------------------------------------------------------------
    // -- GET / POST / Request Helpers
    // ----------------------------------------------------------------------
    
    /**
     * Executes a GET request.
     * 
     * @param string $path The relative path to call.
     * @param array $parameters Optional array of HTTP query parameters
     * @param array $options Array of request options.
     * 
     * @return mixed Decoded response.
     */
    public function get($path, array $parameters = array(), array $options = array())
    {
        return $this->request($path, $parameters, 'GET', $options);
    }
    
    /**
     * Executes a POST request.
     * 
     * @param string $path The relative path to call.
     * @param array $parameters Optional array of HTTP query parameters
     * @param array $options Array of request options.
     * 
     * @return mixed Decoded response.
     */
    public function post($path, array $parameters = array(), array $options = array())
    {
        return $this->request($path, $parameters, 'POST', $options);
    }
    

    /**
     * Executes a PUT request.
     * 
     * @param string $path The relative path to call.
     * @param array $parameters Optional array of HTTP query parameters
     * @param array $options Array of request options.
     * 
     * @return mixed Decoded response.
     */
    public function put($path, array $parameters = array(), array $options = array())
    {
        return $this->request($path, $parameters, 'PUT', $options);
    }
    
    /**
     * Executes a DELETE request.
     * 
     * @param string $path The relative path to call.
     * @param array $parameters Optional array of HTTP query parameters
     * @param array $options Array of request options.
     * 
     * @return mixed Decoded response.
     */
    public function delete($path, array $parameters = array(), array $options = array())
    {
        return $this->request($path, $parameters, 'DELETE', $options);
    }

    /**
     * Generic request method.
     * 
     * @param string $path The relative path to call.
     * @param array $parameters Optional array of HTTP query parameters.
     * @param string $method The HTTP method to use (either GET or POST)
     * @param array $options Array of request options.
     * 
     * @return mixed Decoded response.
     */
    public function request($path, array $parameters = array(), $method = 'GET', array $options = array())
    {
        
        // Get all options
        $options = array_merge($this->_options, $options);
        
        // Create the full url
        $url = $this->createUrl($path, $options);
        //
        $extra_parameters = $this->_addAuthParameters( $options );
        $parameters = $extra_parameters + $parameters;

        // Send request and get response
        $response = $this->execute($url, $parameters, $method, $options);

        // Decode & return
        return $this->decodeResponse($response, $options);
        
    }

    public function _addAuthParameters( $options )
    {
        $parameters = array();
        // Add auth options if logged in
        if ($options['username']) {

            switch ($options['auth_method']) {

                // Login user oAuth
                case Beeminder_Client::AUTH_OAUTH_TOKEN:
                default:
                    $parameters += array(
                        'access_token' => $options['token']
                    );
                    break;

                // Login using private token
                case Beeminder_Client::AUTH_PRIVATE_TOKEN:
                default:
                    $parameters += array(
                        'auth_token' => $options['token']
                    );
                    break;

            }
        }
        return $parameters;
    }


    // ----------------------------------------------------------------------
    // -- Request & Response Helpers
    // ----------------------------------------------------------------------
    
    /**
     * Replace tokens in the url/path with correct values.
     * 
     * @param string $path The path to parse.
     * @param array $options Array of options that will be used when replacing.
     *
     * @return string Full url with tokens replaced.
     */
    public function createUrl($path, $options)
    {
        
        // Add standard elements (path, protocol)
        $url = strtr($options['url'], array(
            ':protocol' => $options['protocol'],
            ':format'   => $options['format'],
            ':path'     => trim($path, '/'),
        ));
        
        // Replace other custom values in paths
        $url = strtr($url, array(
            ':username' => $options['username'],
        ));
        
        return $url;
        
    }
    
    /**
     * Decodes the response from the server. Usually just de-serializing a JSON
     * response.
     * 
     * @param string $response Response content to decode.
     * @param array $options Options used during the request.
     * 
     * @return mixed Decoded response.
     */
    protected function decodeResponse($response, array $options)
    {
        
        if ($options['format'] == 'json') {
            return json_decode($response, true);
        }
        
        throw new Exception(__CLASS__.' only supports json format, '.$options['format'].' given.');
    }
    
}
