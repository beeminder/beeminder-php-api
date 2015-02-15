<?php


class Beeminder_Tests_HttpDriverTest extends PHPUnit_Framework_TestCase
{
    public function testHttpDriver()
    {
        $driver = new Beeminder_HttpDriver_Double();
        $this->assertTrue( $driver instanceof Beeminder_DriverInterface );
    }

    public function testRequestTypes()
    {
        $driver = new Beeminder_HttpDriver_Double();
        $url = 'some url';
        $parameters = array();
        $options = array();

        $this->assertEquals( 'Success!', $driver->get(    $url, $parameters, $options ) );
        $this->assertEquals( 'Success!', $driver->post(   $url, $parameters, $options ) );
        $this->assertEquals( 'Success!', $driver->put(    $url, $parameters, $options ) );
        $this->assertEquals( 'Success!', $driver->delete( $url, $parameters, $options ) );
    }

    // This seems like a YAGNI
    public function testComplainAboutNonJsonFormat()
    {
        $driver = new Beeminder_HttpDriver_Double();
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
        $driver = new Beeminder_HttpDriver_Double();
        $driver->setOption( 'some option', 'some value' );
        $this->assertEquals( 'some value', $driver->getOption('some option') );
    }

    public function testGettingUnsetOptions()
    {
        $driver = new Beeminder_HttpDriver_Double();
        try { 
            $driver->getOption('non-existant option');
                $this->fail('Expected an exception' );
        } catch (Exception $e) {
            // We don't really care what the exception is.
            $message = 'Undefined index: non-existant option';
            $this->assertEquals( $message, $e->getMessage() );
        }
    }

    public function getHttpDriverMock()
    {
        return $this->getMockBuilder( 'Beeminder_HttpDriver' )
            ->setMethods(array('execute'))
            ->getMock();
    }


    // This is not a good test.
    public function dont_testExecute_OAUTH_Token()
    {
        $driver = $this->getHttpDriverMock();

        $more_state = array( 
            'options' => array(
                'username'    => 'oauth user',
                'auth_method' => Beeminder_Client::AUTH_OAUTH_TOKEN,
                'token'       => 'I am an OAUTH token'
            ),
        );

        $bundle = array(
            'url' => 'some url',
            'parameters' => array(),
            'method' => 'GET',
            'options' => $more_state['options'],
        );

        $driver->expects( $this->once() )
            ->method( '_addAuthParameters' )
            ->with( array('hoomo$curl_options' ) )
            ->will( $this->returnValue( 'wiible' ) );

        $response = $driver->request( $bundle['url'], $bundle['parameters'], $bundle['method'], $bundle['options'] );

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

class Beeminder_HttpDriver_Double extends Beeminder_HttpDriver
{
    public function execute($url, array $parameters = array(), $method = 'GET', array $options = array())
    {
        return json_encode( "Success!" );
    }
}
