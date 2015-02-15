<?php

class Beeminder_Tests_HttpDriver_CurlTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $driver = new Beeminder_HttpDriver_Curl();
        $this->assertTrue( $driver instanceof Beeminder_HttpDriver_Curl );

    }

    public function getCurlMock()
    {
        return $this->getMockBuilder( 'Beeminder_HttpDriver_Curl' )
            ->setMethods(array('Curl'))
            ->getMock();
    }

    protected function setupLotsOfState( $state = array() )
    {
        $driver = $this->getCurlMock();

        $url = 'localhost';

        $parameters = array();
        if( array_key_exists( 'parameters', $state ) ) {
            $parameters = $state['parameters'] + $parameters;
        }

        $method = array_key_exists( 'method', $state ) ? $state['method'] : 'GET';

        $options = array(
            'http_port' => 'http_port_value',
            'user_agent' => 'user_agent_value',
            'timeout' => 'timeout_value',
            'username' => '',
            #'auth_method' => Beeminder_Client::AUTH_PRIVATE_TOKEN,
            #'token' => "I'm a private token"
        );
        if( array_key_exists( 'options', $state ) ) {
            $options = $state['options'] + $options;
        }

        $curl_options = array (
                #CURLOPT_URL            => 'localhost?auth_token=I%27m+a+private+token',
                CURLOPT_URL            => $url,
                CURLOPT_USERAGENT      => $options['user_agent'],
                CURLOPT_PORT           => $options['http_port'],
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT        => $options['timeout'],
            );

        if( array_key_exists( 'curl_options', $state ) ) {
            $curl_options = $state['curl_options'] + $curl_options;
        }


        $curl_reply = array(
            'response' => '{ "some":"fake json" }',
            'headers' => array( 'http_code' => 200 ),
            'error_number' => '' 
        );
        if( array_key_exists( 'curl_reply', $state ) ) {
            $curl_reply = $state['curl_reply'] + $curl_reply;
        }

        $driver->expects( $this->once() )
            ->method( 'Curl' )
            ->with( $curl_options )
            ->will( $this->returnValue( $curl_reply ) );

        $state = array (
            'url' => $url,
            'parameters' => $parameters,
            'method' => $method,
            'options' => $options,
            'driver' => $driver,
            'response' => $curl_reply['response'],
        );

        return $state;
    }


    // FIXME: This test doesn't belong with the curl stuff.
    public function testExecute_Method_Delete()
    {
        $query = 'key=value';
        $more_state = array( 
            'method' => 'DELETE',
            'parameters' => array( 'key' => 'value' ),
            'curl_options' => 
                array (
                    CURLOPT_URL        => 'localhost',
                    CURLOPT_POST       => true,
                    CURLOPT_POSTFIELDS => $query,
                    CURLOPT_HTTPHEADER => array('Content-Length: ' . strlen($query)),
                    CURLOPT_CUSTOMREQUEST => 'DELETE'
                ),
        );

        $bundle = $this->setupLotsOfState( $more_state );

        $response = $bundle['driver']->execute( $bundle['url'], $bundle['parameters'], $bundle['method'], $bundle['options'] );

        $this->assertEquals( $bundle['response'], $response );
    }

    public function testExecute_Exception_ErrorNumber()
    {
        $more_state = array( 
            'curl_reply'  => array(
                'error_number' => 1,
                'error_message' => 'error message from the response'
            )
        );
        $bundle = $this->setupLotsOfState( $more_state );

        try {
            $bundle['driver']->execute( $bundle['url'], $bundle['parameters'], $bundle['method'], $bundle['options'] );
            $this->fail('Was expecting an exception');
        } catch (Exception $e) {
            $this->assertEquals( 'error message from the response', $e->getMessage() );
        }
    }

    public function testExecute_Exception_Nota20x()
    {
        $more_state = array( 
            'curl_reply'  => array(
                'headers' => array( 'http_code' => 55 ),
                'response' => 'error message from the response'
            )
        );
        $bundle = $this->setupLotsOfState( $more_state );

        try {
            $bundle['driver']->execute( $bundle['url'], $bundle['parameters'], $bundle['method'], $bundle['options'] );
            $this->fail('Was expecting an exception');
        } catch (Exception $e) {
            $this->assertEquals( 'error message from the response', $e->getMessage() );
        }
    }

    public function testCall()
    {
        $driver = new Beeminder_HttpDriver_Curl();
        $response = $driver->Curl( array() );
        $this->assertEquals( 'No URL set!', $response['error_message'] );
    }
}


