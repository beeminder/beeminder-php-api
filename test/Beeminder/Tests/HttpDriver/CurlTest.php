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

    // FIXME: This test doesnt belong with the curl stuff.
    public function testExecute_OAUTH_Token()
    {

    }

    // FIXME: This test doesnt belong with the curl stuff.
    public function testExecute_AUTH_PRIVATE_Token()
    {

    }

    // FIXME: This test doesnt belong with the curl stuff.
    public function testExecute_QueryParameters()
    {

    }




}
