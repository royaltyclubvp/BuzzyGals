<?php

/**
 * Model_Base_Userstorymedia
 * 
 * @property integer $id
 * @property string $photos
 * @property integer $total
 * @property integer $UserStory
 * @property Model_Userstory $Story
 * 
 * @package     ElleFab
 * @subpackage  Data Access Layer
 * @author      Royalty Club - Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * 
 */
abstract class Model_Base_Userstorymedia extends Doctrine_Record {
    
    public function setTableDefinition() {
        $this->setTableName('userstorymedia');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('photos', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('totalphotos', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'default' => 0,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('videos', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('totalvideos', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'default' => 0,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('userStory', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
    }
    
    public function setUp() {
        parent::setUp();
        $this->hasOne('Model_Userstory as Story', array(
            'local' => 'userStory',
            'foreign' => 'id' 
        ));
    }
}
