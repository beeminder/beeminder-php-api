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



    # 20151210 1009 Old signature
#    public function createDatapoint($goal, $value, $comment = '', $timestamp = null, $sendmail = false )
    public function createDatapoint($slug, $value, $comment = '')
    {
        // Create parameters
        $parameters = array(
            'value'     => $value,
            'comment'   => $comment,
        );

        // Send request
        return $this->createDatapointAdvanced( $slug, $parameters );
    }

    /**
     * Create a single datapoint.
     *
     * @param string $slug The goal slug to retrieve.
     * @param array $parameters An Array of parameters to pass to Beeminder.
     *          You should check the Beeminder API documentation to make sure 
     *          you are sending valid parameters.
     * @return object The created datapoint.
     */
    public function createDatapointAdvanced($slug, $parameters )
    {
        // Send request
        return (object)$this->post("users/:username/goals/{$slug}/datapoints", $parameters);
    }


    /**
     * Add multiple data points.
     */
    public function createDatapoints($slug, $datapoints, $sendmail = false)
    {

        // Create parameters
        $parameters = array(
            'datapoints'=> $datapoints,
            'sendmail'  => $sendmail
        );

        // Send request
        return self::_objectify($this->post("users/:username/goals/{$slug}/datapoints/create_all", $parameters));

    }


    // ----------------------------------------------------------------------
    // -- Editing Datapoints
    // ----------------------------------------------------------------------

    public function editDatapoint($datapointId, $slug, $timestamp = null, $value = null, $comment = null)
    {
        $parameters = array();
        if ($timestamp) $parameters['timestamp'] = $timestamp;
        if ($value)     $parameters['value'] = $value;
        if ($comment)   $parameters['comment'] = $comment;

        return (object)$this->put("users/:username/goals/{$slug}/datapoints/{$datapointId}", $parameters);
    }

    public function updateDatapoint($slug, $datapoint)
    {
        $parameters = array(
            'timestamp' => $datapoint->timestamp,
            'value'     => $datapoint->value,
            'comment'   => $datapoint->comment
        );

        return (object)$this->put("users/:username/goals/{$slug}/datapoints/{$datapoint->id}", $parameters);
    }


    // ----------------------------------------------------------------------
    // -- Deleting Datapoints
    // ----------------------------------------------------------------------

    /**
     * Delete a datapoint.
     *
     * @param string $slug Slug of the goal to delete from.
     * @param string $datapointId ID of the datapoint to delete.
     *
     * @return stdClass The deleted datapoint object.
     */
    public function deleteDatapoint($slug, $datapointId)
    {
        return (object)$this->delete("users/:username/goals/{$slug}/datapoints/{$datapointId}");
    }

}
