<?php
/**
 * 
 * Zend Framework 2 Autoloader [Zend Framework 1 Interface]
 * 
 * @package     Zend Framework 1
 * @subpackage  Library
 * @author      Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * @version     0.1
 */
class Zend2_Loader_Autoloader_Zf2 implements Zend_Loader_Autoloader_Interface {
    public function autoload($class) {
        if(strstr($class, '\\')) {
            $zf2class = explode('\\', $class);
            $path = 'ZF2/'.implode('/', $zf2class).'.php';
            include_once $path; 
        }
    }
}
