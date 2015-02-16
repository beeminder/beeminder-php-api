<?php

class Beeminder_Tests_ApiTest extends PHPUnit_Framework_TestCase
{
    public function testProxyMethods()
    {
       foreach( array( 'get','put','post','delete') as $method ) {
            $api = $this->_getMockApiWithExpectation( $method );
            $api->$method();
       }
    }

    protected function _getMockApiWithExpectation( $method )
    {
        $driver = $this->getMockBuilder( 'Beeminder_HttpDriver' )->getMock();
        $api = new Beeminder_Api_Double( $driver );

        $driver->expects($this->once())->method($method);
        return $api;
    }
}

class Beeminder_Api_Double extends Beeminder_Api
{
    public function get() {
        return parent::get( 'path', array(), array() );
    }

    public function put() {
        return parent::put( 'path', array(), array() );
    }

    public function delete() {
        return parent::delete( 'path', array(), array() );
    }

    public function post() {
        return parent::post( 'path', array(), array() );
    }
}
