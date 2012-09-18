<?php
/**
 * Beeminder_DriverInterface
 * 
 * Interface that all drivers should implement. At the very least there should
 * be a way to retrieve information (get) and a way to set information
 * (post). There should also be a way to get and set options.
 * 
 * @package    Beeminder_Api
 * @author     Phil Newton <phil@sodaware.net>
 * @copyright  2012 Phil Newton <phil@sodaware.net>
 */


interface Beeminder_DriverInterface
{
    
    // ----------------------------------------------------------------------
    // -- Sending GET / POST requests
    // ----------------------------------------------------------------------

    public function get($path, array $parameters = array(), array $options = array());
    public function post($path, array $parameters = array(), array $options = array());
    public function put($path, array $parameters = array(), array $options = array());
    public function delete($path, array $parameters = array(), array $options = array());



    // ----------------------------------------------------------------------
    // -- Getting / Setting Options
    // ----------------------------------------------------------------------

    /**
     * Set an option.
     *
     * @param string $optionName The option to set.
     * @param mixed $optionValue The value to set.
     *
     * @return Beeminder_Driver Current object instance.
     */
    public function setOption($optionName, $optionValue);

}