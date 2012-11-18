<?php


require_once '/Applications/MAMP/htdocs/ef/library/Xpdo/xpdo.class.php';

$xpdo = new xPDO();
$appPath = '/Applications/MAMP/htdocs/ef/application/';

$sources = array(
    'model' => $appPath.'models/main/model/',
    'schema_file' => $appPath.'models/main/model/schema/main.mysql.schema.xml',
);

$manager= $xpdo->getManager();

/*$generator= $xpdo->getGenerator();
 
$generator->parseSchema($sources['schema_file'],$sources['model']);
 
echo 'Done.';
exit();*/