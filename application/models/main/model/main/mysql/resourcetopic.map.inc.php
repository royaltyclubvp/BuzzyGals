<?php
$xpdo_meta_map['Resourcetopic']= array (
  'package' => 'main',
  'version' => '1.1',
  'table' => 'resourcetopic',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'topic' => NULL,
    'resource' => NULL,
  ),
  'fieldMeta' => 
  array (
    'topic' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
    ),
    'resource' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'topic' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'resource' => 
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
    'Resource' => 
    array (
      'class' => 'Resource',
      'local' => 'resource',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Topic' => 
    array (
      'class' => 'Topic',
      'local' => 'topic',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
