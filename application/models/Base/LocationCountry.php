<?php

/**
 * Model_LocationCountry
 * 
 * @property integer $id
 * @property string $name
 * @property Model_Location $UserLocations
 * @property Model_LocationState $States
 * @property Model_LocationCity $Cities
 * 
 * @package     BuzzyGals
 * @subpackage  Data Access Layer
 * @author      Royalty Club - Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * @version     1
 */
abstract class Model_Base_LocationCountry extends Doctrine_Record {
    
    public function setTableDefinition() {
        $this->setTableName('location_country');
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
    }

    public function setUp() {
        parent::setUp();
        $this->hasMany('Model_Location as UserLocations', array(
             'local' => 'id',
             'foreign' => 'countryid'));
        $this->hasMany('Model_LocationCity as Cities', array(
             'local' => 'id',
             'foreign' => 'country'));
        $this->hasMany('Model_LocationState as States', array(
             'local' => 'id',
             'foreign' => 'country'
        ));
    }
}