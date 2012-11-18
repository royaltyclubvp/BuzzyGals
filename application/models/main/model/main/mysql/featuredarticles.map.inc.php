<?php
$xpdo_meta_map['Featuredarticles']= array (
  'package' => 'main',
  'version' => '1.1',
  'table' => 'featuredarticles',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'author' => NULL,
    'avatar_path' => NULL,
    'image_path' => NULL,
    'topic' => NULL,
    'title' => NULL,
    'date' => NULL,
    'available' => NULL,
    'content' => NULL,
    'description' => NULL,
    'url' => NULL,
    'created' => 'CURRENT_TIMESTAMP',
    'modified' => NULL,
  ),
  'fieldMeta' => 
  array (
    'author' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'avatar_path' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
    ),
    'image_path' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
    ),
    'topic' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'title' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '500',
      'phptype' => 'string',
      'null' => false,
    ),
    'date' => 
    array (
      'dbtype' => 'date',
      'phptype' => 'date',
      'null' => false,
    ),
    'available' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '4',
      'phptype' => 'integer',
      'null' => false,
    ),
    'content' => 
    array (
      'dbtype' => 'longtext',
      'phptype' => 'string',
      'null' => false,
    ),
    'description' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'string',
      'null' => false,
    ),
    'url' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
    ),
    'created' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'timestamp',
      'null' => false,
      'default' => 'CURRENT_TIMESTAMP',
    ),
    'modified' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'timestamp',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'topic' => 
    array (
      'alias' => 'topic',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'topic' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'author' => 
    array (
      'alias' => 'author',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'author' => 
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
    'FollowedBy' => 
    array (
      'class' => 'Followedarticle',
      'local' => 'id',
      'foreign' => 'article',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Topic' => 
    array (
      'class' => 'Topic',
      'local' => 'topic',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Author' => 
    array (
      'class' => 'FeaturedAuthor',
      'local' => 'author',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
