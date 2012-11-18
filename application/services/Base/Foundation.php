<?php

/**
 * 
 * Foundation Service Class
 * 
 * @abstract 
 * @package         ElleFab
 * @subpackage      Model Service Layer
 * @author          Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * @version         0.1
 */
abstract class Service_Base_Foundation {
    
    /**
     * $_xpdo
     * 
     * Doctrine Manager Object
     * 
     */
    protected $doctrineManager = null;
    
    public function __construct() {
        $this->doctrineManager = Doctrine_Manager::getInstance();
    }
} 
