<?php
$xpdo_meta_map['Articlecomment']= array (
  'package' => 'main',
  'version' => '1.1',
  'table' => 'articlecomment',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'date' => 'CURRENT_TIMESTAMP',
    'user' => NULL,
    'article' => NULL,
    'content' => NULL,
  ),
  'fieldMeta' => 
  array (
    'date' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'timestamp',
      'null' => false,
      'default' => 'CURRENT_TIMESTAMP',
    ),
    'user' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'article' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'content' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'user' => 
    array (
      'alias' => 'user',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'user' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'article' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'articleid' => 
    array (
      'alias' => 'articleid',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'article' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Article' => 
    array (
      'class' => 'Featuredarticles',
      'local' => 'article',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'User' => 
    array (
      'class' => 'User',
      'local' => 'user',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
