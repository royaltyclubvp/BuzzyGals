<?php
$xpdo_meta_map['Profile']= array (
  'package' => 'main',
  'version' => '1.1',
  'table' => 'profile',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'fName' => NULL,
    'lName' => NULL,
    'displayName' => NULL,
    'gender' => NULL,
    'interests' => NULL,
    'dob' => NULL,
    'email' => NULL,
    'about' => NULL,
    'user' => NULL,
  ),
  'fieldMeta' => 
  array (
    'fName' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '30',
      'phptype' => 'string',
      'null' => false,
    ),
    'lName' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '60',
      'phptype' => 'string',
      'null' => false,
    ),
    'displayName' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'gender' => 
    array (
      'dbtype' => 'enum',
      'precision' => '\'m\',\'f\'',
      'phptype' => 'string',
      'null' => false,
    ),
    'interests' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
    ),
    'dob' => 
    array (
      'dbtype' => 'date',
      'phptype' => 'date',
      'null' => false,
    ),
    'email' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
    ),
    'about' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
    ),
    'user' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
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
