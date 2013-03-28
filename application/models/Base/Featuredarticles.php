<?php

/**
 * Model_Base_Featuredarticles
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $author
 * @property string $avatar_image
 * @property string $title_image
 * @property integer $topic
 * @property string $title
 * @property date $date
 * @property integer $available
 * @property string $content
 * @property string $description
 * @property string $uri
 * @property integer $views
 * @property timestamp $created
 * @property timestamp $modified
 * @property Model_FeaturedAuthor $FeaturedAuthor
 * @property Model_Topic $Topic
 * @property Doctrine_Collection $Articlecomment
 * @property Doctrine_Collection $Followedarticle
 * 
 * @package    ElleFab
 * @subpackage Data Access Layer
 * @author     Royalty Club - Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class Model_Base_Featuredarticles extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('featuredarticles');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('author', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('avatar_image', 'string', 200, array(
             'type' => 'string',
             'length' => 200,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('title_image', 'string', 200, array(
             'type' => 'string',
             'length' => 200,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('topic', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('title', 'string', 500, array(
             'type' => 'string',
             'length' => 500,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('date', 'date', null, array(
             'type' => 'date',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('available', 'integer', 1, array(
             'type' => 'integer',
             'length' => 1,
             'fixed' => false,
             'unsigned' => false,
             'default' => 1,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('blocked', 'integer', 1, array(
             'type' => 'integer',
             'length' => 1,
             'fixed' => false,
             'unsigned' => false,
             'default' => 0,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('content', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('description', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('uri', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             'fixed' => false,
             'unique' => true,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('views', 'integer', 4, array(
             'type' => 'sinteger',
             'length' => 4,
             'fixed' => false,
             'unique' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('created', 'timestamp', null, array(
             'type' => 'timestamp',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('modified', 'timestamp', null, array(
             'type' => 'timestamp',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Model_FeaturedAuthor as Author', array(
             'local' => 'author',
             'foreign' => 'id'));

        $this->hasOne('Model_Topic as Topic', array(
             'local' => 'topic',
             'foreign' => 'id'));

        $this->hasMany('Model_Articlecomment as Comments', array(
             'local' => 'id',
             'foreign' => 'article'));

        $this->hasMany('Model_Followedarticle as Followedarticles', array(
             'local' => 'id',
             'foreign' => 'article'));
        $this->hasMany('Model_User as Followers', array(
            'local' => 'user',
            'foreign' => 'article',
            'refClass' => 'Model_Followedarticle'
        ));
    }
}