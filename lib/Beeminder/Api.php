<?php
/**
 * Beeminder_Api
 * 
 * Base class for API helpers. Each API resource (user, goal etc) has its own
 * helper class that extends from this. Mostly just wraps part of the client.
 * 
 * @package    Beeminder_Api
 * @author     Phil Newton <phil@sodaware.net>
 * @copyright  2012 Phil Newton <phil@sodaware.net>
 */


abstract class Beeminder_Api
{
    
    /**
     * The Beeminder_Driver to make requests with.
     */
    private $_driver;
    
    
    // ----------------------------------------------------------------------
    // -- Construction
    // ----------------------------------------------------------------------
    
    public function __construct(Beeminder_DriverInterface $driver)
    {
        $this->_driver = $driver;
    }
    
    
    // ----------------------------------------------------------------------
    // -- Sending GET/POST requests.
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
    protected function get($path, array $parameters = array(), $requestOptions = array())
    {
        return $this->_driver->get($path, $parameters, $requestOptions);
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
    protected function post($path, array $parameters = array(), $requestOptions = array())
    {
        return $this->_driver->post($path, $parameters, $requestOptions);
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
    protected function put($path, array $parameters = array(), $requestOptions = array())
    {
        return $this->_driver->put($path, $parameters, $requestOptions);
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
    protected function delete($path, array $parameters = array(), $requestOptions = array())
    {
        return $this->_driver->delete($path, $parameters, $requestOptions);
    }
    
    
    // ----------------------------------------------------------------------
    // -- Object result helpers
    // ----------------------------------------------------------------------
    
    protected static function _objectify($values)
    {
        
        // Only work with valid arrays
        if (!$values || !is_array($values)) { 
            return $values;
        }
        
        // Convert each array in the array to an object
        array_walk($values, function(&$value) {
            $value = (object)$value;
        });
        
        return $values;
    }
    
}