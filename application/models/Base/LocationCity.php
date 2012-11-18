<?php

/**
 * Model_LocationCity
 * 
 * @property integer $id
 * @property string $name
 * @property integer $state
 * @property integer $country
 * @property Model_Location $UserLocations
 * @property Model_LocationState $State
 * @property Model_LocationCountry $Country
 * 
 * @package     BuzzyGals
 * @subpackage  Data Access Layer
 * @author      Royalty Club - Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * @version     1
 */
abstract class Model_Base_LocationCity extends Doctrine_Record {
    
    public function setTableDefinition() {
        $this->setTableName('location_city');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('name', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('state', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('country', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'autoincrement' => false,
             ));
    }

    public function setUp() {
        parent::setUp();
        $this->hasMany('Model_Location as UserLocations', array(
             'local' => 'id',
             'foreign' => 'cityid'));
        $this->hasOne('Model_LocationState as State', array(
             'local' => 'state',
             'foreign' => 'id'));
        $this->hasOne('Model_LocationCountry as Country', array(
             'local' => 'country',
             'foreign' => 'id'
        ));
    }
}
