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
    
    
    // ----------------------------------------------------------------------
    // -- Editing Datapoints
    // ----------------------------------------------------------------------

    public function editDatapoint($datapointId, $goal, $timestamp = null, $value = null, $comment = null)
    {
        $parameters = array();
        if ($timestamp) $parameters['timestamp'] = $timestamp;
        if ($value)     $parameters['value'] = $value;
        if ($comment)   $parameters['comment'] = $comment;
        
        return (object)$this->put("users/:username/goals/{$goal}/datapoints/{$datapointId}", $parameters);
    }

    public function updateDatapoint($goalName, $datapoint)
    {
        $parameters = array(
            'timestamp' => $datapoint->timestamp,
            'value'     => $datapoint->value,
            'comment'   => $datapoint->comment
        );
        
        return (object)$this->put("users/:username/goals/{$goalName}/datapoints/{$datapoint->id}", $parameters);
    }
    
    
    // ----------------------------------------------------------------------
    // -- Deleting Datapoints
    // ----------------------------------------------------------------------
    
    /**
     * Delete a datapoint.
     * 
     * @param string $goal Slug of the goal to delete from.
     * @param string $datapointId ID of the datapoint to delete.
     * 
     * @return stdClass The deleted datapoint object.
     */
    public function deleteDatapoint($goal, $datapointId)
    {
        return (object)$this->delete("users/:username/goals/{$goal}/datapoints/{$datapointId}");
    }
    
}