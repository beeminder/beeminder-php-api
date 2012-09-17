<?php
/**
 * Beeminder_HttpDriver_Curl
 * 
 * Curl based HTTP driver. Used to interact with the API over HTTP using the
 * CURL library.
 * 
 * @package    BeeminderApi
 * @subpackage HttpDrivers
 * @author     Phil Newton <phil@sodaware.net>
 * @copyright  2012 Phil Newton <phil@sodaware.net>
 */


class Beeminder_HttpDriver_Curl extends Beeminder_HttpDriver
{
    
    // ----------------------------------------------------------------------
    // -- Sending Requests
    // ----------------------------------------------------------------------
    
    /**
     * Sends a request to a URI and returns the response as plaintext.
     *
     * @param string $path The path to send a request to.
     * @param array $parameters Request query parameters
     * @param string $method The HTTP method to use - either GET or POST
     * @param array $options Optional request options.
     *
     * @return string The HTTP response contents.
     */
    public function execute($url, array $parameters = array(), $method = 'GET', array $options = array())
    {
        
        // Initialize curl options
        $curlOptions = array(
            CURLOPT_URL            => $url,
            CURLOPT_PORT           => $options['http_port'],
            CURLOPT_USERAGENT      => $options['user_agent'],
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => $options['timeout']
        );
        
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
        
        // Add query parameters
        if (!empty($parameters)) {
            
            // Encode args
            $query = utf8_encode(http_build_query($parameters, '', '&'));
            
            // Append for GET, set as POST variables otherwise
            if ('GET' === $method) {
                $curlOptions[CURLOPT_URL] = "{$url}?{$query}";
            } else {
                $curlOptions += array(
                    CURLOPT_POST       => true,
                    CURLOPT_POSTFIELDS => $query
                );
            }
            
        }
        
        // Call Curl
        $response = $this->_call($curlOptions);
        
        // Check for errors
        if (!in_array($response['headers']['http_code'], array(0, 200, 201))) {
            throw new Exception(null, (int) $response['headers']['http_code']);
        }
        
        if ($response['error_number'] != '') {
            throw new Exception($response['error_message'], $response['error_number']);
        }

        return $response['response'];
        
    }


    // ----------------------------------------------------------------------
    // -- Internal Execution Helpers
    // ----------------------------------------------------------------------
    
    /**
     * Call CURL with the specified options.
     * 
     * @param array Associative array of CURL options.
     * @return array Array containing response, headers and any error details.
     */
    protected function _call(array $curlOptions)
    {
        $curl = curl_init();

        curl_setopt_array($curl, $curlOptions);

        // Fetch response & headers
        $response = array(
            'response'      => curl_exec($curl),
            'headers'       => curl_getinfo($curl),
            'error_number'  => curl_errno($curl),
            'error_message' => curl_error($curl)
        );

        curl_close($curl);
        
        return $response;
    }
    
}