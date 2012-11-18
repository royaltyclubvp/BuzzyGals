<?php
$xpdo_meta_map['User']= array (
  'package' => 'main',
  'version' => '1.1',
  'table' => 'user',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'username' => NULL,
    'password' => NULL,
    'salt' => NULL,
    'created' => NULL,
    'modified' => NULL,
    'status' => 0,
    'lastLogin' => NULL,
    'accessFailures' => NULL,
    'usergroup' => NULL,
  ),
  'fieldMeta' => 
  array (
    'username' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '30',
      'phptype' => 'string',
      'null' => false,
      'index' => 'unique',
    ),
    'password' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => true,
    ),
    'salt' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '30',
      'phptype' => 'string',
      'null' => false,
    ),
    'created' => 
    array (
      'dbtype' => 'date',
      'phptype' => 'date',
      'null' => false,
    ),
    'modified' => 
    array (
      'dbtype' => 'date',
      'phptype' => 'date',
      'null' => false,
    ),
    'status' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '4',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'lastLogin' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'timestamp',
      'null' => true,
    ),
    'accessFailures' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'usergroup' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
  ),
  'indexes' => 
  array (
    'username' => 
    array (
      'alias' => 'username',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'username' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'userType' => 
    array (
      'alias' => 'userType',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'usergroup' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'ArticleComments' => 
    array (
      'class' => 'Articlecomment',
      'local' => 'id',
      'foreign' => 'user',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'FollowedArticles' => 
    array (
      'class' => 'Followedarticles',
      'local' => 'id',
      'foreign' => 'user',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'FollowedTopics' => 
    array (
      'class' => 'Followedtopic',
      'local' => 'id',
      'foreign' => 'user',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'IncomingFriendRequests' => 
    array (
      'class' => 'Friendrequest',
      'local' => 'id',
      'foreign' => 'requestee',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'OutgoingFriendRequests' => 
    array (
      'class' => 'Friendrequest',
      'local' => 'id',
      'foreign' => 'requestor',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Friends' => 
    array (
      'class' => 'Friendship',
      'local' => 'id',
      'foreign' => 'user',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'SentMessages' => 
    array (
      'class' => 'Messages',
      'local' => 'id',
      'foreign' => 'sender',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Profile' => 
    array (
      'class' => 'Profile',
      'local' => 'id',
      'foreign' => 'user',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
    'ReceivedMessages' => 
    array (
      'class' => 'Recipient',
      'local' => 'id',
      'foreign' => 'user',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Group' => 
    array (
      'class' => 'Usergroup',
      'local' => 'group',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Stories' => 
    array (
      'class' => 'Userstory',
      'local' => 'id',
      'foreign' => 'user',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'StoryComments' => 
    array (
      'class' => 'Userstorycomment',
      'local' => 'id',
      'foreign' => 'user',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
