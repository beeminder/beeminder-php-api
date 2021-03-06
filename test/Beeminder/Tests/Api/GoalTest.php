<?php

require_once __DIR__ . '/../ApiTestCase.php';

/********
 * It looks like we're mocking the object under test here:
 *  $api = $this->getApiMockObject();
 *
 * But.. we're only mocking out 4 methods: ('get', 'post', 'delete', 'put')
 *
 * TODO: Extract those into a different object and only mock that.
 *
 *******/

class Beeminder_Tests_Api_GoalTest extends Beeminder_Tests_ApiTestCase
{

    public function testGetGoal()
    {
        $api = $this->getApiMockObject();

        $api->expects($this->once())
            ->method('get')
            ->with('users/:username/goals/goal-1', array());

        $api->getGoal("goal-1");
    }

    public function testGetGoalWithDatapoints()
    {
        $api = $this->getApiMockObject();

        $api->expects($this->once())
            ->method('get')
            ->with('users/:username/goals/goal-1', array('datapoints'=>'true'));

        $api->getGoalWithDatapoints("goal-1");
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

    public function testEditGoal()
    {
        $api = $this->getApiMockObject();

        $goal = 'slug';
        $options = array( 'key' => 'value' );

        $api->expects($this->once())
            ->method('put')
            ->with('users/:username/goals/' . $goal, $options );

        $api->editGoal( $goal, $options );
    }

    public function testUpdateGoal()
    {
        $api = $this->getApiMockObject();

        $goal = (object) array( 'slug' => 'value', 'unexpected' => 'will be filtered out' );
        $expected = array( 'slug' => 'value' );


        $api->expects($this->once())
            ->method('put')
            ->with('users/:username/goals/' . $goal->slug , $expected );

        $api->updateGoal( $goal );
    }

    public function testUpdatableParameters()
    {
        $api  = $this->getApiMockObject();

        $goal = (object) array( 'hello' => 'there' );
        $this->assertEquals( array(), $api->updatableGoalParameters( $goal ) );

        $api->updatableGoalParameters = array('hello');
        $this->assertEquals( array('hello'=>'there'), $api->updatableGoalParameters( $goal ) );
    }

    public function testUpdateGoalWithoutSlug()
    {
        $api = $this->getApiMockObject();

        $goal = (object) array( 'anotherkey' => 'anothervalue' );

        try { 
            $api->updateGoal( $goal );
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals( 'Undefined property: stdClass::$slug', $e->getMessage() );
        }

    }

    public function testUpdateGoalLanewidth()
    {
        $api = $this->getApiMockObject();

        $goal = (object) array( 'slug' => 'value', 'lanewidth' => 12345 );

        $expected_options = array( 
            'slug' => 'value',
            'lanewidth' => 12345,
        );

        $api->expects($this->once())
            ->method('put')
            ->with('users/:username/goals/' . $goal->slug , $expected_options );

        $api->updateGoal( $goal );
    }


    public function testUpdateGoalRoadAll()
    {
        $api = $this->getApiMockObject();

        $goal = (object) array( 'slug' => 'value', 'roadall' => array( array("road matrix")) );
        $expected_options = array( 'slug' => 'value', 'roadall' => $goal->roadall );

        $api->expects($this->once())
            ->method('put')
            ->with('users/:username/goals/' . $goal->slug , $expected_options );

        $api->updateGoal( $goal );
    }

    public function testUpdateRoad()
    {
        $api = $this->getApiMockObject();

        $slug = 'a_slug';

        $options = array (
            'rate'  => 1, 'date'  => 2, 'value' => 3,
        );

        $expected_options = array (
            'rate'     => 1, 'goaldate' => 2, 'goalval'  => 3,
        );

        $api->expects($this->once())
            ->method('post')
            ->with("users/:username/goals/{$slug}/dial_road" , $expected_options );

        $api->updateRoad( $slug, $options['rate'], $options['date'], $options['value'] );

    }

    public function testCreateGoal()
    {
        $api = $this->getApiMockObject();

        $parameters = array();
        $expected_options = $parameters;

        $api->expects($this->once())
            ->method('post')
            ->with("users/:username/goals" , $expected_options );

        $api->createGoal( $parameters );
    }

    public function testUpdateSlug()
    {
        $api = $this->getApiMockObject();

        # Should we validate slugs?
        $goal = (object) array( 'slug' => 'original_slug');
        $new_slug = (string) 'some_new_slug';

        $parameters = array( 'slug' => $new_slug );
        $expected_options = $parameters;

        $api->expects($this->once())
            ->method('put')
            ->with(sprintf('users/:username/goals/%s',$goal->slug), $expected_options );

        $api->updateSlug( $goal, $new_slug );
    }


}
