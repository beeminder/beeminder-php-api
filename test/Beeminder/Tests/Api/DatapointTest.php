<?php

require_once __DIR__ . '/../ApiTestCase.php';

class Beeminder_Tests_Api_DatapointTest extends Beeminder_Tests_ApiTestCase
{

    // ----------------------------------------------------------------------
    // -- Fetching Datapoints
    // ----------------------------------------------------------------------

    public function testGetGoalDatapoints()
    {
        $api = $this->getApiMockObject();

        $api->expects($this->once())
            ->method('get')
            ->with('users/:username/goals/goal-1/datapoints')
            ->will($this->returnValue(array()));

        $data = $api->getGoalDatapoints("goal-1");

        $this->assertEquals(array(), $data, "->getGoalDatapoints() returns empty array for empty set");
    }

    public function testGetGoalDatapointsConversion()
    {
        $api = $this->getApiMockObject();

        $api->expects($this->once())
            ->method('get')
            ->with('users/:username/goals/goal-1/datapoints')
            ->will($this->returnValue($this->_getTestDatapointsResult()));

        $data = $api->getGoalDatapoints("goal-1");

        $this->assertEquals(2, count($data), "->getGoalDatapoints() returns empty array for empty set");
    }


    // ----------------------------------------------------------------------
    // -- Creating Data
    // ----------------------------------------------------------------------

    public function testCreateDatapoint()
    {
        $api = $this->getApiMockObject();

        $parameters = array(
            'value'     => 123,
            'comment'   => 'Test Datapoint 1',
        );

        $api->expects($this->once())
            ->method('post')
            ->with('users/:username/goals/goal-1/datapoints', $parameters);

        $newPoint = $api->createDatapoint('goal-1', 123, 'Test Datapoint 1');
    }


    public function testCreateDatapointFloatingPoint()
    {
        $api = $this->getApiMockObject();

        $expected = array(
            'value'   => 123.456,
            'comment' => '',
        );

        $api->expects($this->once())
            ->method('post')
            ->with('users/:username/goals/goal-1/datapoints', $expected);

        $newPoint = $api->createDatapoint('goal-1', 123.456 );
    }

    public function testCreateDatapointDefaultComment()
    {
        $api = $this->getApiMockObject();

        $parameters = array(
            'value'     => 123,
            'comment'   => '',
        );

        $api->expects($this->once())
            ->method('post')
            ->with('users/:username/goals/goal-1/datapoints', $parameters);

        $newPoint = $api->createDatapoint('goal-1', 123);
    }

    public function testCreateDatapointAdvanced()
    {
        $api = $this->getApiMockObject();

        $slug = 'goal-1';

        $parameters = array(
            'value'                 => 123,
            'comment'               => '',
            'timestamp'             => 123456789,
            'requestid'             => 'unique',
            'daystamp'              => '20151210',
            'not_used_by_beeminder' => 'but we will send it anyway',
        );

        $api->expects($this->once())
            ->method('post')
            ->with('users/:username/goals/goal-1/datapoints', $parameters);

        $newPoint = $api->createDatapointAdvanced( $slug, $parameters );
    }



    public function testCreateDatapoints()
    {
        $api = $this->getApiMockObject();

        $parameters = array(
            'datapoints' => array( 
                'timestamp' => 1,
                'value'     => 123,
                'comment'   => 'Test Datapoint 1',
            ),
            'sendmail'  => false,
        );

        $api->expects($this->once())
            ->method('post')
            ->with('users/:username/goals/goal-1/datapoints/create_all', $parameters);

        $api->createDatapoints('goal-1', $parameters['datapoints'], $parameters['sendmail']);
    }

    // ----------------------------------------------------------------------
    // -- Deleting
    // ----------------------------------------------------------------------

    public function testDeleteDatapoint()
    {
        $api = $this->getApiMockObject();

        $api->expects($this->once())
            ->method('delete')
            ->with('users/:username/goals/goal-1/datapoints/1')
            ->will($this->returnValue($this->_getTestDatapoint()));

        $deleted = $api->deleteDatapoint('goal-1', 1);

        $this->assertEquals($this->_getTestDatapoint(), $deleted, "->deleteDatapoint() returns deleted item");
    }


    // ----------------------------------------------------------------------
    // -- Editing / Updating.
    // ----------------------------------------------------------------------


    public function testEditDatapoint()
    {
        $api = $this->getApiMockObject();

        $goal = 'goal-1';
        $datapointsID = 1234;
        $parameters = array(
            'timestamp' => 1,
            'value'     => 123,
            'comment'   => 'Test Datapoint 1',
        );

        $api->expects($this->once())
            ->method('put')
            ->with('users/:username/goals/goal-1/datapoints/1234', $parameters);
#            ->will($this->returnValue($this->_getTestDatapoint()));

        $api->editDatapoint( $datapointsID, $goal, $parameters['timestamp'], $parameters['value'], $parameters['comment'] );

    }

    public function testUpdateDatapoint()
    {
        $api = $this->getApiMockObject();

        $goal = 'goal-1';
        $parameters = array(
            'timestamp' => 1,
            'value'     => 123,
            'comment'   => 'Test Datapoint 1',
        );
        $datapoint = (object) ($parameters + array( 'id' => 1234 ));

        $api->expects($this->once())
            ->method('put')
            ->with('users/:username/goals/goal-1/datapoints/1234', $parameters);

        $api->updateDatapoint( $goal, $datapoint );

    }

    // ----------------------------------------------------------------------
    // -- Test Data
    // ----------------------------------------------------------------------

    protected function _getTestDatapointsResult()
    {
        return array(

            array(
                'timestamp'  => 1,
                'value'      => 13,
                'id'         => 1,
                'updated_at' => 1,
                'comment'    => 'Test Datapoint 1',
            ),

            array(
                'timestamp'  => 2,
                'value'      => 19,
                'id'         => 2,
                'updated_at' => 2,
                'comment'    => 'Test Datapoint 2',
            )
        );
    }

    protected function _getTestDatapoint()
    {
        return (object)array(
            'timestamp'  => 1,
            'value'      => 123,
            'id'         => 1,
            'updated_at' => 1,
            'comment'    => 'Test Datapoint 1',
        );
    }

}
