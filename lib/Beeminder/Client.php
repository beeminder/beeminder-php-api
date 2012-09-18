<?php
/**
 * Beeminder_Client
 * 
 * Base class for API helpers. Each API resource (user, goal etc) has its own
 * helper class that extends from this. Mostly just wraps part of the client.
 * 
 * @package    Beeminder_Api
 * @author     Phil Newton <phil@sodaware.net>
 * @copyright  2012 Phil Newton <phil@sodaware.net>
 */


class Beeminder_Client
{
    
    // ------------------------------------------------------------
    // -- Authentication Methods
    // ------------------------------------------------------------
    
    /**
     * Authentication method - use the private auth token.
     */
    const AUTH_PRIVATE_TOKEN = 1;
    
    /**
     * Authentication method - use an oauth token.
     */
    const AUTH_OAUTH_TOKEN = 2;
    
    /**
     * The driver to use when accessing the API. Usually Beeminder_HttpDriver_Curl.
     */
    private $_driver;
    
    /**
     * Cache of api instances.
     */
    private $_apiCache;
    
    
    // ------------------------------------------------------------
    // -- Construction
    // ------------------------------------------------------------
    
    /**
     * Create a new client with an optional driver override.
     */
    public function __construct(Beeminder_DriverInterface $driver = null)
    {
        $this->_apis = array();

        $this->_driver = ($driver != null) ?
            $driver : new Beeminder_HttpDriver_Curl();
    }
    
    
    // ------------------------------------------------------------
    // -- Authentication
    // ------------------------------------------------------------

    /**
     * Set user authentication for all methods that follow.
     * 
     * @param string $username Beeminder username.
     * @param string $token Private token OR 
     * @param string $method Authentication method to use (One of the AUTH_* constants)
     */
    public function login($username, $token, $method = null)
    {
        
        if (!$method) {
            $method = self::AUTH_PRIVATE_TOKEN;
        }
        
        // Set driver options
        $this->getDriver()
            ->setOption('auth_method', $method)
            ->setOption('username', $username)
            ->setOption('token', $token);
        
    }

    /**
     * Log the current user out.
     */
    public function logout()
    {
        $this->login(null, null, null);
    }
    
    
    // ------------------------------------------------------------
    // -- Fetch API's
    // ------------------------------------------------------------
    
    /**
     * Get the User resource API.
     * @return Beeminder_Api_User API object for querying User objects.
     */
    public function getUserApi()
    {
        return $this->_getApi('User');
    }
    
    /**
     * Get the Goal resource API.
     * @return Beeminder_Api_Goal API object for querying Goal objects.
     */
    public function getGoalApi()
    {
        return $this->_getApi('Goal');
    }
    
    /**
     * Get the Datapoint resource API.
     * @return Beeminder_Api_Datapoint API object for querying Datapoint objects.
     */
    public function getDatapointApi()
    {
        return $this->_getApi('Datapoint');
    }
    
    /**
     * Helper method for fetching resource API objects. Checks if a class to
     * handle the request exists. If it does, will return an instance of
     * it. Also caches objects for performace.
     * 
     * @param string $name Name of the resource, such as User or Goal. Case-sensitive.
     * 
     * @return Beeminder_Api Either an object of Beeminder_Api_$name, or null if class not found.
     */
    protected function _getApi($name)
    {
        if (!isset($this->_apiCache[$name])) {
            $className = 'Beeminder_Api_' . $name;
            $this->_apiCache[$name] = new $className($this->getDriver());
        }
        
        return $this->_apiCache[$name];
    }
    
    
    // ------------------------------------------------------------
    // -- Getters
    // ------------------------------------------------------------
    
    /**
     * Get the driver (usually a Beeminder_HttpDriver)
     */
    public function getDriver()
    {
        return $this->_driver;
    }
    
    
    // ------------------------------------------------------------
    // -- Querying the API
    // ------------------------------------------------------------
    
    /**
     * Retrieves information from an API path. Fetching a specific resource api
     * (such as Beeminder_Api_Goal) and callign a helper method is preferred
     * over using this.
     * 
     * @param string $path The relative path to call.
     * @param array $parameters Optional array of query parameters.
     * @param array $requestOptions Optional array of options.
     * 
     * @return mixed Decoded response.
     */
    public function get($path, array $parameters = array(), array $requestOptions = array())
    {
        return $this->getDriver()->get($path, $parameters, $requestOptions);
    }
    
    /**
     * Sends information to an API path. Fetching a specific resource api (such
     * as Beeminder_Api_Goal) and callign a helper method is preferred over
     * using this.
     * 
     * @param string $path The relative path to call.
     * @param array $parameters Optional array of values to send.
     * @param array $requestOptions Optional array of options.
     * 
     * @return mixed Decoded response.
     */
    public function post($path, array $parameters = array(), $requestOptions = array())
    {
        return $this->getDriver()->post($path, $parameters, $requestOptions);
    }
    
}