<?php
$xpdo_meta_map['Usergroup']= array (
  'package' => 'main',
  'version' => '1.1',
  'table' => 'usergroup',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => NULL,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'string',
      'null' => false,
    ),
  ),
  'aggregates' => 
  array (
    'User' => 
    array (
      'class' => 'User',
      'local' => 'id',
      'foreign' => 'usergroup',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
