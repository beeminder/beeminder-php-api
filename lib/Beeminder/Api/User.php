<?php
/**
 * Beeminder_Api_User
 * 
 * API helper for working with User resources.
 * 
 * @package    BeeminderApi
 * @subpackage API
 * @author     Phil Newton <phil@sodaware.net>
 * @copyright  2012 Phil Newton <phil@sodaware.net>
 */


class Beeminder_Api_User extends Beeminder_Api
{   
    
    const FILTER_ALL         = 'all';
    const FILTER_FRONTBURNER = 'frontburner';
    const FILTER_BACKBURNER  = 'backburner';
    
    
    // ----------------------------------------------------------------------
    // -- Fetching Information
    // ----------------------------------------------------------------------
    
    /**
     * Fetch information about the currently logged in user.
     * 
     * @return stdClass User information.
     */
    public function getUser($filter = self::FILTER_ALL)
    {
        return (object)$this->get("users/:username", array(
            'goals_filter' => $filter
        ));
    }
        
}