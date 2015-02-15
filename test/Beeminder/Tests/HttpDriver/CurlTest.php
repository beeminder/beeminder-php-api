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
            ->setMethods(array('_call'))
            ->getMock();
    }

    public function testExecute()
    {
        $driver = $this->getCurlMock();

        $url = 'localhost';
        $parameters = array();
        $method = 'GET';

        $options = array(
            'http_port' => 823,
            'user_agent' => 'testing',
            'timeout' => 5,
            'username' => null,
        );

        $curl_options = array (
                CURLOPT_URL            => 'localhost',
                CURLOPT_USERAGENT      => 'testing',
                CURLOPT_PORT           => 823,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT        => 5,
            );

        $some_fake_json = '{ "hello":"world" }';
        $curl_reply = array(
            'response' => $some_fake_json,
            'headers' => array( 'http_code' => 200 ),
            'error_number' => '' 
        );

        $driver->expects( $this->once() )
            ->method( '_call' )
            ->with( $curl_options )
            ->will( $this->returnValue( $curl_reply ) );

        $response = $driver->execute( $url, $parameters, $method, $options );

        $this->assertEquals( $some_fake_json, $response );
    }

    // FIXME: This test doesn't belong with the curl stuff.
    public function testExecute_OAUTH_Token()
    {
        $driver = $this->getCurlMock();

        $url = 'localhost';
        $parameters = array();
        $method = 'GET';

        $options = array(
            'http_port' => 823,
            'user_agent' => 'testing',
            'timeout' => 5,
            'username' => 'some username',
            'auth_method' => Beeminder_Client::AUTH_OAUTH_TOKEN,
            'token' => "I'm an oauth token"
        );

        $curl_options = array (
                CURLOPT_URL            => 'localhost?access_token=I%27m+an+oauth+token',
                CURLOPT_USERAGENT      => 'testing',
                CURLOPT_PORT           => 823,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT        => 5,
            );

        $some_fake_json = '{ "hello":"world" }';
        $curl_reply = array(
            'response' => $some_fake_json,
            'headers' => array( 'http_code' => 200 ),
            'error_number' => '' 
        );

        $driver->expects( $this->once() )
            ->method( '_call' )
            ->with( $curl_options )
            ->will( $this->returnValue( $curl_reply ) );

        $response = $driver->execute( $url, $parameters, $method, $options );

        $this->assertEquals( $some_fake_json, $response );
    }

    // FIXME: This test doesn't belong with the curl stuff.
    public function testExecute_AUTH_PRIVATE_Token()
    {
        $driver = $this->getCurlMock();

        $url = 'localhost';
        $parameters = array();
        $method = 'GET';

        $options = array(
            'http_port' => 823,
            'user_agent' => 'testing',
            'timeout' => 5,
            'username' => 'some username',
            'auth_method' => Beeminder_Client::AUTH_PRIVATE_TOKEN,
            'token' => "I'm a private token"
        );

        $curl_options = array (
                CURLOPT_URL            => 'localhost?auth_token=I%27m+a+private+token',
                CURLOPT_USERAGENT      => 'testing',
                CURLOPT_PORT           => 823,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT        => 5,
            );

        $some_fake_json = '{ "hello":"world" }';
        $curl_reply = array(
            'response' => $some_fake_json,
            'headers' => array( 'http_code' => 200 ),
            'error_number' => '' 
        );

        $driver->expects( $this->once() )
            ->method( '_call' )
            ->with( $curl_options )
            ->will( $this->returnValue( $curl_reply ) );

        $response = $driver->execute( $url, $parameters, $method, $options );

        $this->assertEquals( $some_fake_json, $response );
    }

    public function setupLotsOfState( $state = array() )
    {
        $driver = $this->getCurlMock();

        $url = 'localhost';
        $parameters = array();
        $method = array_key_exists( 'method', $state ) ? $state['method'] : 'GET';

        $options = array(
            'http_port' => 823,
            'user_agent' => 'testing',
            'timeout' => 5,
            'username' => 'some username',
            'auth_method' => Beeminder_Client::AUTH_PRIVATE_TOKEN,
            'token' => "I'm a private token"
        );

        $curl_options = array (
                CURLOPT_URL            => 'localhost?auth_token=I%27m+a+private+token',
                CURLOPT_USERAGENT      => 'testing',
                CURLOPT_PORT           => 823,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT        => 5,
            );

        if( array_key_exists( 'curl_options', $state ) ) {
            $curl_options = $state['curl_options'] + $curl_options;
        }

        $some_fake_json = '{ "hello":"world" }';

        $curl_reply = array(
            'response' => $some_fake_json,
            'headers' => array( 'http_code' => 200 ),
            'error_number' => '' 
        );
        if( array_key_exists( 'curl_reply', $state ) ) {
            $curl_reply = $state['curl_reply'] + $curl_reply;
        }

        $driver->expects( $this->once() )
            ->method( '_call' )
            ->with( $curl_options )
            ->will( $this->returnValue( $curl_reply ) );

        $bundle = array (
            'url' => $url,
            'parameters' => $parameters,
            'method' => $method,
            'options' => $options,
            'driver' => $driver,
            'response' => $some_fake_json,
        );

        return $bundle;
    }

    // FIXME: This test doesn't belong with the curl stuff.
    public function testExecute_Method_Delete()
    {
        $query = 'auth_token=I%27m+a+private+token';
        $more_state = array( 
            'method' => 'DELETE',
            'curl_options' => 
                array (
                    CURLOPT_URL        =>  'localhost',
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
           # $this->assertEquals( 'error message from the response', $e->getMessage() );
            $this->assertEquals( '', $e->getMessage() );
        }


    }




}
