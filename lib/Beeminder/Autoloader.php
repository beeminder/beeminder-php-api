<?php
/**
 * Beeminder_Autoloader
 * 
 * Autoloads classes used by the Beeminder API.
 * 
 * @package    Beeminder_Api
 * @author     Phil Newton <phil@sodaware.net>
 * @copyright  2012 Phil Newton <phil@sodaware.net>
 */


class Beeminder_Autoloader
{
    
    /**
     * Registers Beeminder_Autoloader.
     */
    static public function register()
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(new self, 'autoload'));
    }
    
    /**
     * Autoloads Beeminder api classes. Any class that is prefixed with
     * "Beeminder" will be loaded automatically by this function.
     *
     * @param string $className The class name to autoload.
     *
     * @return boolean True if class loaded, false if not.
     */
    static public function autoload($className)
    {
        
        // Only process classes in Beeminder package
        if (strpos($className, 'Beeminder') === false) {
            return false;
        }
        
        // Load file if it exists
        $fileName = dirname(__FILE__) . '/../' . str_replace('_', '/', $className) . '.php';
        if (file_exists($fileName)) {
            require_once $fileName;
            return true;
        }

        return false;
        
    }
    
}