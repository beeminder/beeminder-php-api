<?php
// Abstract class

abstract class Beeminder_Tests_ApiTest extends PHPUnit_Framework_TestCase
{
    
    protected function _getMockName()
    {
        $class = get_class($this);
        $class = str_replace('Tests_', '', $class);
        $class = substr($class, 0, -4);
        return $class;
    }

    public function getApiMockObject()
    {
        return $this->getMockBuilder($this->_getMockName())
            ->setMethods(array('get', 'post'))
            ->disableOriginalConstructor()
            ->getMock();
    }
}