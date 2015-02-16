<?php


class Beeminder_Tests_HttpDriverTest extends PHPUnit_Framework_TestCase
{
    private function getHttpDriverMock()
    {
        $stub = $this->getMockBuilder( 'Beeminder_HttpDriver' )
            ->setMethods(array('execute'))
            ->getMock();

        $stub->expects( $this->any() )->method( 'execute' )
            ->will( $this->returnValue( json_encode( "Success!" ) ) );

        return $stub;
    }

    public function testRequestTypes()
    {
        $driver = $this->getHttpDriverMock();
        $path = 'some path';
        $parameters = array();
        $options = array();

        $this->assertEquals( 'Success!', $driver->get(    $path, $parameters, $options ) );
        $this->assertEquals( 'Success!', $driver->post(   $path, $parameters, $options ) );
        $this->assertEquals( 'Success!', $driver->put(    $path, $parameters, $options ) );
        $this->assertEquals( 'Success!', $driver->delete( $path, $parameters, $options ) );
    }

    // This seems like a YAGNI
    public function testComplainAboutNonJsonFormat()
    {
        $driver = $this->getHttpDriverMock();

        $url = 'some url';
        $parameters = array();
        $options = array( 'format' => '!json' );

        try {
            $driver->get( $url, $parameters, $options );
            $this->fail( "Expected an exception" );
        } catch (Exception $e ) {
            $message = "Beeminder_HttpDriver only supports json format, !json given.";
            $this->assertEquals( $message, $e->getMessage() );
        }

    }

    public function testSettingOptions()
    {
        $driver = $this->getHttpDriverMock();
        $driver->setOption( 'some option', 'some value' );
        $this->assertEquals( 'some value', $driver->getOption('some option') );
    }

    public function testGettingUnsetOptions()
    {
        $driver = $this->getHttpDriverMock();
        try { 
            $driver->getOption('non-existant option');
                $this->fail('Expected an exception' );
        } catch (Exception $e) {
            // We don't really care what the exception is.
            $message = 'Undefined index: non-existant option';
            $this->assertEquals( $message, $e->getMessage() );
        }
    }

    public function testAddAuthParameters()
    {
        $driver = $this->getHttpDriverMock();

        $auth_options = array(
                'username'    => 'oauth user',
                'auth_method' => Beeminder_Client::AUTH_OAUTH_TOKEN,
                'token'       => 'I am an OAUTH token'
            );

        $expected = array( 
            'url'        => "https://www.beeminder.com/api/v1/some_path.json",
            'parameters' => array( 'access_token' => $auth_options['token'] ),
            'method'     => 'GET',
            'options'    => $auth_options + $driver->getOptions(),
        );

        $this->_addExecuteCallExpectation( $driver, $expected );
        $response = $driver->request( 'some_path', array(), 'GET', $auth_options );
    }

    private function _addExecuteCallExpectation( $driver, $expected )
    {
        $driver->expects( $this->once() )
            ->method( 'execute' )
            ->with( $expected['url'],
                    $expected['parameters'],
                    $expected['method'],
                    $expected['options'] );
    }

    public function test_Params_OAUTH_Token()
    {
        $driver = $this->getHttpDriverMock();
        $options = array(
            'username'    => 'oauth user',
            'auth_method' => Beeminder_Client::AUTH_OAUTH_TOKEN,
            'token'       => 'I am an OAUTH token'
        );
        $params = $driver->_addAuthParameters( $options );
        $this->assertEquals( array( 'access_token' => $options['token'] ), $params );
    }

    public function test_Params_AUTH_PRIVATE_Token()
    {
        $driver = $this->getHttpDriverMock();
        $options = array(
                'username'    => 'private auth user',
                'auth_method' => Beeminder_Client::AUTH_PRIVATE_TOKEN,
                'token'       => 'I am a private token'
        );
        $params = $driver->_addAuthParameters( $options );
        $this->assertEquals( array( 'auth_token' => $options['token'] ), $params );
    }

}

