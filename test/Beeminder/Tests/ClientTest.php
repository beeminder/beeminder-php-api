<?php

class Beeminder_Tests_ClientTest extends PHPUnit_Framework_TestCase
{

    // ------------------------------------------------------------
    // -- Creation
    // ------------------------------------------------------------

    public function testCreateWithoutDriver()
    {
        $client = new Beeminder_Client();

        $this->assertInstanceOf('Beeminder_HttpDriver', $client->getDriver());
    }

    public function testCreateWidthHttpClientDriver()
    {
        $driver = $this->getHttpDriverMock();
        $client = new Beeminder_Client($driver);

        $this->assertEquals($driver, $client->getDriver());
    }

    # See: http://semver.org/
    public function testHasSemVerVersion()
    {
        $client = new Beeminder_Client();
        $this->assertRegExp( "/^\d+\.\d+\.\d+/", Beeminder_Client::VERSION );
    }


    // ------------------------------------------------------------
    // -- Authentication Tests
    // ------------------------------------------------------------

    public function testLogin()
    {
        $username = 'test_user';
        $token    = 'test_token';
        $method   = 'method';

        $driver = $this->getHttpDriverMock();
        $driver->expects($this->exactly(3))
            ->method('setOption')
            ->will($this->returnValue($driver));

        $client = $this->getClientMockBuilder()
            ->setMethods(array('getDriver'))
            ->getMock();

        $client->expects($this->once())
            ->method('getDriver')
            ->with()
            ->will($this->returnValue($driver));

        $client->login($username, $token, $method);
    }

    public function testLoginWithoutMethod()
    {
        $username = 'test_user';
        $token    = 'test_token';

        $driver = $this->getHttpDriverMock();

        $driver->expects($this->any())
           ->method('setOption')
           ->will($this->returnValue($driver));

        $driver->expects($this->at(0))
           ->method('setOption')
           ->with( 'auth_method', Beeminder_Client::AUTH_PRIVATE_TOKEN )
           ->will($this->returnValue($driver));

        $client = $this->getClientMockBuilder()
            ->setMethods(array('getDriver'))
            ->getMock();

        $client->expects($this->once())
            ->method('getDriver')
            ->with()
            ->will($this->returnValue($driver));

        $client->login($username, $token);
    }


    public function testLogout()
    {

        $client = $this->getClientMockBuilder()
            ->setMethods(array('login'))
            ->getMock();

        $client->expects($this->once())
            ->method('login')
            ->with(null, null, null);

        $client->logout();
    }


    // ------------------------------------------------------------
    // -- Request Tests
    // ------------------------------------------------------------

    public function testGet()
    {
        $path      = '/example/path/';
        $parameters = array('query_var' => 'query_value');
        $options    = array('option_name' => 'option_value');

        $driver = $this->getHttpDriverMock();
        $driver->expects($this->once())
            ->method('get')
            ->with($path, $parameters, $options);

        $client = $this->getClientMockBuilder()
            ->setMethods(array('getDriver'))
            ->getMock();
        $client->expects($this->once())
            ->method('getDriver')
            ->with()
            ->will($this->returnValue($driver));

        $client->get($path, $parameters, $options);
    }

    public function testPost()
    {
        $path      = '/example/path/';
        $parameters = array('query_var' => 'query_value');
        $options    = array('option_name' => 'option_value');

        $driver = $this->getHttpDriverMock();
        $driver->expects($this->once())
            ->method('post')
            ->with($path, $parameters, $options);

        $client = $this->getClientMockBuilder()
            ->setMethods(array('getDriver'))
            ->getMock();
        $client->expects($this->once())
            ->method('getDriver')
            ->with()
            ->will($this->returnValue($driver));

        $client->post($path, $parameters, $options);
    }


    // ------------------------------------------------------------
    // -- API Helpers
    // ------------------------------------------------------------

    public function testGetUserApiHelper()
    {
        $client = new Beeminder_Client();
        $this->assertInstanceOf('Beeminder_Api_User', $client->getUserApi());
    }

    public function testGetGoalApiHelper()
    {
        $client = new Beeminder_Client();
        $this->assertInstanceOf('Beeminder_Api_Goal', $client->getGoalApi());
    }

    public function testGetDatapointApiHelper()
    {
        $client = new Beeminder_Client();
        $this->assertInstanceOf('Beeminder_Api_Datapoint', $client->getDatapointApi());
    }


    // ------------------------------------------------------------
    // -- Mock Helpers
    // ------------------------------------------------------------

    protected function getClientMockBuilder()
    {
        return $this->getMockBuilder('Beeminder_Client')
            ->disableOriginalConstructor();
    }

    protected function getHttpDriverMock()
    {
        return $this->getMockBuilder('Beeminder_HttpDriver')
            ->getMock();
    }

}
