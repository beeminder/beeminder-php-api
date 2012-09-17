<?php

require_once __DIR__ . '/../ApiTest.php';

class Beeminder_Tests_Api_DatapointTest extends Beeminder_Tests_ApiTest
{
    
    // ----------------------------------------------------------------------
    // -- Fetching Datapoints
    // ----------------------------------------------------------------------
    
    public function testGetGoalDatapoints()
    {
        $api = $this->getApiMockObject();
        
        $api->expects($this->once())
            ->method('get')
            ->with('users/:username/goals/goal-1/datapoints.json')
            ->will($this->returnValue(array()));
        
        $data = $api->getGoalDatapoints("goal-1");
        
        $this->assertEquals(array(), $data, "->getGoalDatapoints() returns empty array for empty set");
    }
    
    public function testGetGoalDatapointsConversion()
    {
        $api = $this->getApiMockObject();
        
        $api->expects($this->once())
            ->method('get')
            ->with('users/:username/goals/goal-1/datapoints.json')
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
            'timestamp' => 1,
            'value'     => 123,
            'comment'   => 'Test Datapoint 1',
            'sendmail'  => false,
        );
            
        $api->expects($this->once())
            ->method('post')
            ->with('users/:username/goals/goal-1/datapoints.json', $parameters)
            ->will($this->returnValue($this->_getTestDatapoint()));
        
        $newPoint = $api->createDatapoint('goal-1', 123, 'Test Datapoint 1', 1, false);
        
        $this->assertEquals($this->_getTestDatapoint(), $newPoint, "->createDatapoint() returns created item");
    }

    public function testCreateDatapointWithoutDetails()
    {
        $api = $this->getApiMockObject();
        
        $parameters = array(
            'value'     => 123,
            'timestamp' => time(),
            'comment'   => '',
            'sendmail'  => false
        );
        
        $expectedResult = $this->_getTestDatapoint();
        $expectedResult->timestamp = $parameters['timestamp'];
        $expectedResult->comment   = '';
        $expectedResult->update_at = $parameters['timestamp'];

        $api->expects($this->once())
            ->method('post')
            ->with('users/:username/goals/goal-1/datapoints.json', $parameters)
            ->will($this->returnValue($expectedResult));
        
        $newPoint = $api->createDatapoint('goal-1', 123);
        
        $this->assertEquals($expectedResult, $newPoint, "->createDatapoint() returns created item");
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