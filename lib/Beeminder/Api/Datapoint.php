<?php
/**
 * Beeminder_Api_Datapoint
 * 
 * API helper for working with Datapoint resources. Use these when tracking any
 * kind of data.
 * 
 * @package    BeeminderApi
 * @subpackage API
 * @author     Phil Newton <phil@sodaware.net>
 * @copyright  2012 Phil Newton <phil@sodaware.net>
 */


class Beeminder_Api_Datapoint extends Beeminder_Api
{   
    
    // ----------------------------------------------------------------------
    // -- Fetching Information
    // ----------------------------------------------------------------------
    
    /**
     * Fetch all datapoints for a single goal.
     * 
     * @param string $slug The goal slug to retrieve.
     * @return array Array of datapoint objects.
     */
    public function getGoalDatapoints($slug)
    {
        // Fetch datapoints
        $datapoints = $this->get("users/:username/goals/{$slug}/datapoints");
        
        // Convert to array of objects (if present)
        return self::_objectify($datapoints);
    }
    
    
    // ----------------------------------------------------------------------
    // -- Creating Datapoints
    // ----------------------------------------------------------------------
    
    public function createDatapoint($goal, $value, $comment = '', $timestamp = null, $sendmail = false)
    {
        
        // Create parameters
        $parameters = array(
            'timestamp' => ($timestamp == null) ? time() : $timestamp,
            'value'     => (int)$value,
            'comment'   => $comment,
            'sendmail'  => $sendmail
        );
        
        // Send request
        return (object)$this->post("users/:username/goals/{$goal}/datapoints", $parameters);
        
    }
    
    
}