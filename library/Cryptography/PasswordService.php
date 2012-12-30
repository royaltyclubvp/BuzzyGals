<?php
/**
 * Provides the ability to hash password using bcrypt and verify plaintext
 * 
 * @package     Cryptography
 * @subpackage  Services
 * @version     1
 * @author      Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 */
use Zend\Crypt\Password\Bcrypt;

class Cryptography_PasswordService extends Bcrypt {
    
    public function __construct($cost = NULL) {
       parent::__construct();
       if($cost != NULL) {
           $this->setCost($cost);
       }
    }
}
