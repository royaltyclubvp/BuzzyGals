<?php

/**
 * Model_Base_Followedstory
 * 
 * 
 * @property integer $id
 * @property timestamp $date
 * @property integer $user
 * @property integer $story
 * @property Model_User $User
 * @property Model_Userstory $Userstory
 * 
 * @package    ElleFab
 * @subpackage Data Access Layer
 * @author     Royalty Club - Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * 
 */
abstract class Model_Base_Followedstory extends Doctrine_Record {
    
    public function setTableDefinition() {
        $this->setTableName('followedstory');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('user', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('story', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('date', 'timestamp', null, array(
             'type' => 'timestamp',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Model_Userstory as Story', array(
             'local' => 'story',
             'foreign' => 'id'));

        $this->hasOne('Model_User as User', array(
             'local' => 'user',
             'foreign' => 'id'));
    }
}
