<?php

/**
 * Model_Base_Followedarticle
 * 
 * @property integer $id
 * @property integer $article
 * @property integer $user
 * @property string  $reason
 * @property Model_Featuredarticles $FeaturedArticle
 * @property Model_User $User
 * 
 * @package     BuzzyGals
 * @subpackage  Data Access Layer
 * @author      Royalty Club - Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * 
 */
abstract class Model_Base_Flaggedarticle extends Doctrine_Record {
    public function setTableDefinition() {
        $this->setTableName('flaggedarticle');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('article', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
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
        $this->hasColumn('reason', 'string', null, array(
            'type' => 'string',
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => true,
            'autoincrement' => false
        ));
    }
    
    public function setUp() {
        parent::setUp();
        $this->hasOne('Model_Featuredarticles as Article', array(
             'local' => 'article',
             'foreign' => 'id'));

        $this->hasOne('Model_User as User', array(
             'local' => 'user',
             'foreign' => 'id'));
    }
}
