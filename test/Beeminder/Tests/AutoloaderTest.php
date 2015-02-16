<?php

class Beeminder_Tests_AutoloaderTest extends PHPUnit_Framework_TestCase
{
    
    public function testAutoloadInvalidClass()
    {
        $this->assertFalse(class_exists('SomeNonexistantClass'), '->autoload() will not load none-Beeminder classes');
    }

    public function testManuallyLoadInvalidClass()
    {
        $autoloader = new Beeminder_Autoloader();
        $this->assertFalse($autoloader->autoload('SomeNonexistantClass'), '->autoload() returns false when class not loaded');
    }
    
    public function testAutoloadsBeeminderClasses()
    {
        $this->assertTrue(class_exists('Beeminder_Client'), '->autoload() loads Beeminder classes');
    }

    public function testManuallyLoadBeeminderClass()
    {
        $autoloader = new Beeminder_Autoloader();
        $this->assertTrue($autoloader->autoload('Beeminder_Api'), '->autoload() returns true when class loaded');
    }

    public function testManuallyLoadNonExistantBeeminderClass()
    {
        $autoloader = new Beeminder_Autoloader();
        $this->assertFalse($autoloader->autoload('Beeminder_Nonexistant'));
    }

    public function testRegister()
    {
        $function = array( 'Beeminder_Autoloader', 'autoload' );
        spl_autoload_unregister( $function );
        $this->assertFalse( in_array( $function, spl_autoload_functions() ));
        Beeminder_Autoloader::register();
        $this->assertTrue( in_array( $function, spl_autoload_functions() ));
    }

}
