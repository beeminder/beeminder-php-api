<?php

class Beeminder_Tests_HttpDriver_CurlTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $driver = new Beeminder_HttpDriver_Curl();
        $this->assertTrue( $driver instanceof Beeminder_HttpDriver_Curl );

    }

    protected function getCurlMock()
    {
        return $this->getMockBuilder( 'Beeminder_HttpDriver_Curl' )
            ->setMethods(array('Curl'))
            ->getMock();
    }

    protected function callExecute( $bundle ) 
    {
        return $bundle['driver']->execute( 
            $bundle['url'],
            $bundle['parameters'],
            $bundle['method'],
            $bundle['options']
        );
    }

    protected function setupLotsOfState( $state = array() )
    {
        $driver = $this->getCurlMock();
        $parameters = $this->_setupExecuteParameters( $driver, $state );

        $curl_options = $driver->_initialiseCurlOptions( $parameters['url'], $parameters['options'] );
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

        $bundle = $parameters + array (
            'driver' => $driver,
            'response' => $curl_reply['response'],
        );

        return $bundle;
    }

    // Parameters to the execute call.
    protected function _setupExecuteParameters( $driver, $state )
    {
        $balls['url'] = 'localhost';

        $balls['parameters'] = array();
        if( array_key_exists( 'parameters', $state ) ) {
            $balls['parameters'] = $state['parameters'] + $balls['parameters'];
        }

        $balls['method'] = array_key_exists( 'method', $state ) ? $state['method'] : 'GET';

        $balls['options'] = $driver->getOptions();
        if( array_key_exists( 'options', $state ) ) {
            $balls['options'] = $state['options'] + $balls['options'];
        }

        return $balls;
    }


    public function testExecute_Method_Get()
    {
        $query = 'key=value';
        $more_state = array( 
            'method' => 'GET',
            'parameters' => array( 'key' => 'value' ),
            'curl_options' => 
                array ( CURLOPT_URL => 'localhost?key=value',),
        );

        $bundle = $this->setupLotsOfState( $more_state );

        $response = $this->callExecute( $bundle );

        $this->assertEquals( $bundle['response'], $response );
    }

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
        $response = $this->callExecute( $bundle );

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
            $response = $this->callExecute( $bundle );
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
            $response = $this->callExecute( $bundle );
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

    public function testEncodeQueryConvertsArraysToJson()
    {
        $driver = new Beeminder_HttpDriver_Curl();
        $parameters = array( 'greeting' => array( 'hello', 'world') );
        $expected = sprintf( "greeting=%s", urlencode( json_encode( $parameters['greeting'] ) ));
        $this->assertEquals( $expected, $driver->_encodeQuery( $parameters ) );
    }

}


