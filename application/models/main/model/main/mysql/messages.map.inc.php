<?php
$xpdo_meta_map['Messages']= array (
  'package' => 'main',
  'version' => '1.1',
  'table' => 'messages',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'sender' => NULL,
    'date' => NULL,
    'type' => NULL,
    'content' => NULL,
    'reference' => NULL,
    'subject' => NULL,
  ),
  'fieldMeta' => 
  array (
    'sender' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'index',
    ),
    'date' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => false,
    ),
    'type' => 
    array (
      'dbtype' => 'enum',
      'precision' => '\'n\',\'f\',\'r\'',
      'phptype' => 'string',
      'null' => false,
    ),
    'content' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'string',
      'null' => false,
    ),
    'reference' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => true,
      'index' => 'index',
    ),
    'subject' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'sender' => 
    array (
      'alias' => 'sender',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'sender' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'reference' => 
    array (
      'alias' => 'reference',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'reference' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => true,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Sender' => 
    array (
      'class' => 'User',
      'local' => 'sender',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'ParentMessage' => 
    array (
      'class' => 'Messages',
      'local' => 'reference',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
