<?php

/**
 * Model_Location
 * 
 * @property integer $id
 * @property string $city
 * @property string $stateprov
 * @property string $country
 * @property string $cityid
 * @property string $stateprovid
 * @property string $countryid
 * @property Model_Resource $Resources
 * @property Model_Profile $UserProfiles
 * 
 * @package     ElleFab
 * @subpackage  Data Access Layer
 * @author      Royalty Club - Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * @version     1
 */
abstract class Model_Base_Location extends Doctrine_Record {
    
    public function setTableDefinition() {
        $this->setTableName('location');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
         $this->hasColumn('cityid', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'autoincrement' => false,
             ));
         $this->hasColumn('stateprovid', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'autoincrement' => false,
             ));
         $this->hasColumn('countryid', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'autoincrement' => false,
             ));
         $this->hasColumn('city', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
         $this->hasColumn('stateprov', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
         $this->hasColumn('country', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
    }

    public function setUp() {
        parent::setUp();
        $this->hasMany('Model_Resource as Resources', array(
             'local' => 'id',
             'foreign' => 'location'));
        $this->hasMany('Model_Profile as UserProfiles', array(
             'local' => 'id',
             'foreign' => 'location'));
        $this->hasOne('Model_LocationCity as City', array(
             'local' => 'cityid',
             'foreign' => 'id'
        ));
        $this->hasOne('Model_LocationState as State', array(
             'local' => 'stateprovid',
             'foreign' => 'id'
        ));
        $this->hasOne('Model_LocationCountry as Country', array(
             'local' => 'countryid',
             'foreign' => 'id'
        ));
    }
}
