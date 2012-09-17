<?php

require __DIR__ . '/../ApiTest.php';

class Beeminder_Tests_Api_GoalTest extends Beeminder_Tests_ApiTest
{
    
    public function testGetGoal()
    {
        $api = $this->getApiMockObject();
        
        $api->expects($this->once())
            ->method('get')
            ->with('users/:username/goals/goal-1');
        
        $api->getGoal("goal-1");
    }
    
    public function testGetGoals()
    {
        $api = $this->getApiMockObject();
        
        $api->expects($this->once())
            ->method('get')
            ->with('users/:username/goals')
            ->will($this->returnValue(array('goal', 'goal2')));
        
        $api->getGoals();
    }
    
}