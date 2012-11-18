<?php
$xpdo_meta_map['Resource']= array (
  'package' => 'main',
  'version' => '1.1',
  'table' => 'resource',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => NULL,
    'content' => NULL,
    'type' => NULL,
    'location_specific' => NULL,
    'location' => NULL,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'content' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'string',
      'null' => false,
    ),
    'type' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'location_specific' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '4',
      'phptype' => 'integer',
      'null' => false,
    ),
    'location' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '4',
      'phptype' => 'integer',
      'null' => true,
    ),
  ),
  'composites' => 
  array (
    'ResourceTopic' => 
    array (
      'class' => 'Resourcetopic',
      'local' => 'id',
      'foreign' => 'resource',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
  ),
);
