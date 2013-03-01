<?php
/**
 * Provides the ability to hash string using PHP hash function
 * 
 * @package     Cryptography
 * @subpackage  Services
 * @version     1
 * @author      Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 */
use Zend\Crypt\Hash;

class Cryptography_HashingService {
    
    public $algorithm = 'sha256';
    
    public function __construct($algorithm = NULL) {
        if($algorithm != NULL) {
            $this->algorithm = $algorithm;
        }
    } 
    
    public function compute($data) {
        return Hash::compute($this->algorithm, $data);
    }
}
