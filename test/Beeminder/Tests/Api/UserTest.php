<?php

require_once __DIR__ . '/../ApiTest.php';

class Beeminder_Tests_Api_UserTest extends Beeminder_Tests_ApiTest
{
    
    // ----------------------------------------------------------------------
    // -- Fetching user information
    // ----------------------------------------------------------------------
    
    public function testGetUser()
    {
        $api = $this->getApiMockObject();
        
        $api->expects($this->once())
            ->method('get')
            ->with('users/:username')
            ->will($this->returnValue($this->_getTestUser('all')));
        
        $user = $api->getUser();
        
        $this->assertEquals($this->_getTestUser('all'), $user, "->getUser() returns object");
    }

    public function testGetUserWithFilter()
    {
        $api = $this->getApiMockObject();
        
        $api->expects($this->once())
            ->method('get')
            ->with('users/:username', array('goals_filter' => 'backburner'))
            ->will($this->returnValue($this->_getTestUser('backburner')));
        
        $user = $api->getUser(Beeminder_Api_User::FILTER_BACKBURNER);
        
        $this->assertEquals($this->_getTestUser('backburner'), $user, "->getUser() returns backburner goals");
    }
    
    
    // ----------------------------------------------------------------------
    // -- Test Data
    // ----------------------------------------------------------------------

    protected function _getTestUser($filter = 'all')
    {
        $user = array(
            'username'   => 'test_user',
            'updated_at' => 123,
            'goals'      => array()
        );
        
        switch($filter) {
        case 'all':
            $user['goals'] = array('frontburner_goal', 'backburner_goal');
            break;
        case 'backburner':
            $user['goals'] = array('backburner_goal');
            break;
        case 'frontburner':
            $user['goals'] = array('frontburner_goal');
            break;
        }

        return (object)$user;
    }


}