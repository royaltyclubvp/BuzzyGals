<?php

/**
 * Model_Articlecomment
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ElleFab
 * @subpackage Data Access Layer
 * @author     Royalty Club - Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Model_Articlecomment extends Model_Base_Articlecomment
{
    public function setUp() {
        parent::setUp();
        $this->hasOne('Model_Profile as UserProfile', array(
             'local' => 'user',
             'foreign' => 'user'));
    }
}