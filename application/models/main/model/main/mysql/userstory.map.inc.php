<?php
$xpdo_meta_map['Userstory']= array (
  'package' => 'main',
  'version' => '1.1',
  'table' => 'userstory',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'date' => NULL,
    'content' => NULL,
    'user' => NULL,
  ),
  'fieldMeta' => 
  array (
    'date' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => false,
    ),
    'content' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'string',
      'null' => false,
    ),
    'user' => 
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
      ),
    ),
  ),
  'aggregates' => 
  array (
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
