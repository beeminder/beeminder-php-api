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
        $curlOptions = $this->_initialiseCurlOptions( $url, $options );

        // FIXME: There is horribleness hiding in here.
        $extra_curlOptions = $this->_addQueryParameters( $url, $parameters, $method );
        $curlOptions = $extra_curlOptions + $curlOptions;

        $extra_curlOptions = $this->_setCallType( $method );
        $curlOptions = $extra_curlOptions + $curlOptions;

        // Call Curl
        $response = $this->Curl($curlOptions);

        $this->_checkForErrors( $response );

        return $response['response'];

    }

    // ----------------------------------------------------------------------
    // -- Internal Execution Helpers
    // ----------------------------------------------------------------------

    public function _initialiseCurlOptions( $url, $options )
    {
        return array(
            CURLOPT_URL            => $url,
            CURLOPT_PORT           => $options['http_port'],
            CURLOPT_USERAGENT      => $options['user_agent'],
            CURLOPT_TIMEOUT        => $options['timeout'],
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        );
    }



    protected function _addQueryParameters( $url, $parameters, $method )
    {
        $curlOptions = array();

        // Add query parameters
        if (!empty($parameters)) {

            // Encode args
            $query = $this->_encodeQuery( $parameters );

            // Add payload data
            switch ($method) {

                case 'GET':
                    $curlOptions[CURLOPT_URL] = "{$url}?{$query}";
                    break;

                case 'DELETE':
                case 'PUT':
                case 'POST':
                    $curlOptions += array(
                        CURLOPT_POST       => true,
                        CURLOPT_POSTFIELDS => $query,
                        CURLOPT_HTTPHEADER => array('Content-Length: ' . strlen($query))
                    );
                    break;
            }
        }
        return $curlOptions;
    }

    protected function _encodeQuery( $parameters )
    {
        $query = utf8_encode(http_build_query($parameters, '', '&'));
        return $query;
    }

    protected function _setCallType( $method )
    {
        $curlOptions = array();
        // Set call type (if not post/get)
        if ($method == 'DELETE' || $method == 'PUT') {
            $curlOptions[CURLOPT_CUSTOMREQUEST] = $method;
        }
        return $curlOptions;
    }


    /**
     * Check to see if there are any errors in the response.
     *
     * @param  - array result of the curl request.
     * @return - nothing. Will throw an exeception if there are any errors.
     */
    protected function _checkForErrors( $response )
    {
        $this->_checkForErrorsFromRequest( $response );
        $this->_checkForErrorsFromCurl( $response );
    }

    protected function _checkForErrorsFromRequest( $response )
    {
        if (!in_array($response['headers']['http_code'], array(0, 200, 201))) {
            throw new Exception( $response['response'], (int) $response['headers']['http_code']);
        }
    }

    protected function _checkForErrorsFromCurl( $response )
    {
        if ($response['error_number'] != '') {
            throw new Exception($response['error_message'], $response['error_number']);
        }
    }

    /**
     * Call CURL with the specified options.
     *
     * @param array Associative array of CURL options.
     * @return array Array containing response, headers and any error details.
     */
    public function Curl(array $curlOptions)
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
