<?php

/**
 * Model_Base_Bookmarkedresource
 * 
 * 
 * @property integer $id
 * @property timestamp $date
 * @property integer $user
 * @property integer $resource
 * @property Model_User $User
 * @property Model_Resource $Resource
 * 
 * @package    ElleFab
 * @subpackage Data Access Layer
 * @author     Royalty Club - Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * 
 */
abstract class Model_Base_Bookmarkedresource extends Doctrine_Record {
    
    public function setTableDefinition() {
        $this->setTableName('bookmarkedresource');
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
        $this->hasColumn('resource', 'integer', 4, array(
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

    public function setUp() {
        parent::setUp();
        $this->hasOne('Model_Resource as Resource', array(
             'local' => 'resource',
             'foreign' => 'id'));

        $this->hasOne('Model_User as User', array(
             'local' => 'user',
             'foreign' => 'id'));
    }
}
