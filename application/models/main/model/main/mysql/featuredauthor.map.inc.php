<?php
$xpdo_meta_map['FeaturedAuthor']= array (
  'package' => 'main',
  'version' => '1.1',
  'table' => 'featured_author',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => NULL,
    'created' => 'CURRENT_TIMESTAMP',
    'modified' => NULL,
  ),
  'fieldMeta' => 
  array (
    'name' => 
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
  'composites' => 
  array (
    'Articles' => 
    array (
      'class' => 'Featuredarticles',
      'local' => 'id',
      'foreign' => 'author',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
