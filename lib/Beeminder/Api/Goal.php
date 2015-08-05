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
     * @param boolan $includeDatapoints whether to include the goal's datapoints
     * @return stdClass Object containing the goal data.
     */
    public function getGoal($slug, $includeDatapoints = false)
    {
        $params = array();

        if($includeDatapoints) {
            # 20150805 - Beeminder API currently requires a
            # literal string rather than boolean.
            $params['datapoints'] = 'true';
        }

        return (object)$this->get("users/:username/goals/{$slug}", $params );
    }

    /**
     * Fetch goal data including datapoints.
     *
     * @param string $slug Goal slug to retrieve.
     * @return stdClass Object containing the goal data with datapoints included.
     *
     */
    public function getGoalWithDatapoints($slug)
    {
        return $this->getGoal($slug, true);
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
    
    /**
     * Update a goal object
     *
     * Note: if you need to change the slug you should call updateSlug.
     *
     * @param stdClass altered goal object to update.
     * @return array Array of goals objects.
     */
    public function updateGoal(stdClass $goal)
    {
        $required = array( 'slug' );
        $optional = array( 'title', 'panic', 'secret', 'datapublic', 'roadall');
        foreach( array_merge( $required, $optional ) as $parameter )
        {
            if( property_exists( $goal, $parameter ) ) {
                $parameters[$parameter] = $goal->$parameter;
            }
        }

        return $this->editGoal( $goal->slug, $parameters );
    }

    /*
     * Update the slug of a goal.
     */
    public function updateSlug( stdClass $goal, $new_slug )
    {
        $parameters = array( 'slug' => $new_slug );
        return $this->editGoal( $goal->slug, $parameters );
    }

    public function editGoal($goal, array $options)
    {
        return (object)$this->put("users/:username/goals/{$goal}", $options);
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
