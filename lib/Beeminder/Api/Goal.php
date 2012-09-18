<?php
/**
 * Beeminder_Api_Goal
 * 
 * API helper for working with Goal resources.
 * 
 * @package    BeeminderApi
 * @subpackage API
 * @author     Phil Newton <phil@sodaware.net>
 * @copyright  2012 Phil Newton <phil@sodaware.net>
 */


class Beeminder_Api_Goal extends Beeminder_Api
{
    
    const FILTER_ALL         = 'all';
    const FILTER_FRONTBURNER = 'frontburner';
    const FILTER_BACKBURNER  = 'backburner';
    
    
    // ----------------------------------------------------------------------
    // -- Fetching Information
    // ----------------------------------------------------------------------
    
    /**
     * Fetch a list of the current user's goals.
     * 
     * @param string $filter The filter to use when retrieving goals. See FILTER_ constants.
     * @return array Array of goals objects.
     */
    public function getGoals($filter = Beeminder_Api_Goal::FILTER_ALL)
    {
        // Fetch goals
        $goals = $this->get("users/:username/goals");
        
        // Convert to array of objects
        array_walk($goals, function(&$goal) {
            $goal = (object)$goal;
        });
        
        return $goals;
    }
    
    /**
     * Fetch information about a single goal.
     * 
     * @param string $slug Goal slug to retrieve.
     * @return stdClass Object containing 
     */
    public function getGoal($slug, $includeDatapoints = false)
    {
        return (object)$this->get("users/:username/goals/{$slug}", array(
            'datapoints' => $includeDatapoints
        ));
    }
    
    
    // ----------------------------------------------------------------------
    // -- Creating Goals
    // ----------------------------------------------------------------------
    
    /**
     * Create a new goal for a user.
     * 
     * Requires the following fields:
     *   - slug
     *   - title
     *   - goal_type
     *   - goaldate
     *   - goalval
     *   - rate
     *
     * The following fields are optional:
     *   - ephem
     *   - panic
     *   - secret
     *   - datapublic
     * 
     * @param array $fields Array of values for the new goal.
     * 
     * @return stdClass The newly created goal object.
     */
    public function createGoal(array $fields = array())
    {
        return (object)$this->post("users/:username/goals", $fields);
    }
    
    
    // ----------------------------------------------------------------------
    // -- Updating Goals
    // ----------------------------------------------------------------------
    
    public function editGoal($goal, array $options)
    {
        return (object)$this->put("users/:username/goals/{$goal}", $options);
    }
    
    public function updateGoal($goal)
    {
        
        $parameters = array(
            'slug'       => $goal->slug,
            'title'      => $goal->title,
            'ephem'      => $goal->ephem,
            'panic'      => $goal->panic,
            'secret'     => $goal->secret,
            'datapublic' => $goal->datapublic,
        );
        
        return (object)$this->put("users/:username/goals/{$goal->slug}", $parameters);
        
    }
    
    
    // ----------------------------------------------------------------------
    // -- Updating Yellow Brick Road
    // ----------------------------------------------------------------------
    
    /**
     * Update the yellow brick road for a goal.
     */
    public function updateRoad($goal, $rate = null, $date = null, $value = null)
    {
        $parameters = array(
            'rate'     => $rate,
            'goaldate' => $date,
            'goalval'  => $value,
        );
        
        return (object)$this->post("users/:username/goals/{$goal}/dial_road", $parameters);
        
    }

}