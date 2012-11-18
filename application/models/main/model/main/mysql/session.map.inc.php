<?php
$xpdo_meta_map['Session']= array (
  'package' => 'main',
  'version' => '1.1',
  'table' => 'session',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'sessionid' => NULL,
    'data' => NULL,
    'expires' => NULL,
  ),
  'fieldMeta' => 
  array (
    'sessionid' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '200',
      'phptype' => 'string',
      'null' => false,
      'index' => 'pk',
    ),
    'data' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
    ),
    'expires' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
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
        'sessionid' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
