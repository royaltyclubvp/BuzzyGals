<?php

/**
 * Model_FollowedstoryTable
 */
class Model_FollowedstoryTable extends Doctrine_Table {
    /**
     * Returns an instance of this class.
     *
     * @return object Model_FollowedstoryTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Model_Followedstory');
    }
 
}
