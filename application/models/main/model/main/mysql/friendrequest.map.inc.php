<?php
$xpdo_meta_map['Friendrequest']= array (
  'package' => 'main',
  'version' => '1.1',
  'table' => 'friendrequest',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'requestor' => NULL,
    'requestee' => NULL,
    'date' => NULL,
    'new' => NULL,
  ),
  'fieldMeta' => 
  array (
    'requestor' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'requestee' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'date' => 
    array (
      'dbtype' => 'date',
      'phptype' => 'date',
      'null' => false,
    ),
    'new' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '4',
      'phptype' => 'integer',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'requestor' => 
    array (
      'alias' => 'requestor',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'requestor' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'requestee' => 
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
    'Requestor' => 
    array (
      'class' => 'User',
      'local' => 'requestor',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Requestee' => 
    array (
      'class' => 'User',
      'local' => 'requestee',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
