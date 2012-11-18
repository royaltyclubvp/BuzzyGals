<?php

/**
 * Doctrine Zend_Auth Database Persistence Class [Doctrine Version 1.2.4]
 * 
 * @package     ElleFab
 * @subpackage  Zend_Auth
 * @version     1
 * @author      Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 */
 
 class Auth_Adapter_AuthDoctrineDbStorage implements Zend_Auth_Storage_Interface {
     private static $_session = 'session';
     
     private static $_useridfield = 'userid';
     
     private static $_sessionidfield = 'id';
     
     private static $_hostnamefield = 'hostname';
     
     private static $_accessedfield = 'accessed';
     
     private static $_datafield = 'userdata';
     
     private static $_cookieName;
     
     private static $_dbrow = null;
     /**
      * Constructor
      * 
      * @param      string      $cookieName
      * @param      bool        $keepLoggedIn      
      */
     public function __construct($cookieName) {
         self::$_cookieName = $cookieName;
     }
     
     /**
      * Checks if Database Row Exists
      * 
      * @throws Zend_Auth_Storage_Exception If operation could not be completed
      * @return bool
      */
      public function isEmpty() {
         $requestObject = new Zend_Controller_Request_Http();
         if($cookie = $requestObject->getCookie(self::$_cookieName, FALSE)) {
             //Decrypt Cookie
             $encryption = new Cryptography_EncryptionService('1111834');
             $decrypted = $encryption->decrypt($cookie);
             //Separate Session ID from UserID
             $sessioncookie = explode('||', $decrypted);
             $sessionid = $sessioncookie[0];
             //Check Session Table
             try {
                $session = Doctrine_Core::getTable('Model_Session')->findOneBy(self::$_sessionidfield, $sessionid);
             }
             catch (Doctrine_Exception $e) {
                 throw new Zend_Auth_Storage_Exception();
             }
             
             if(empty($session)) return true;
             self::$_dbrow = $session;
             
             return false;
         }
         else {
             return true;
         }
      }
      
      /**
       * Returns content of storage
       * 
       * @throws Zend_Auth_Storage_Exception IF operation could not be completed
       * @return mixed
       */
       public function read() {
           if(self::$_dbrow != NULL) {
               self::$_dbrow->{self::$_accessedfield} = time();
               try {
                   self::$_dbrow->save();
               }
               catch (Doctrine_Exception $e) {
                   throw new Zend_Auth_Storage_Exception();
               }
               return unserialize(self::$_dbrow->{self::$_datafield});
           }
           else {
               $requestObject = new Zend_Controller_Request_Http();
               $cookie = $requestObject->getCookie(self::$_cookieName, 0);
               //Decrypt Cookie
                $encryption = new Cryptography_EncryptionService('1111834');
                $decrypted = $encryption->decrypt($cookie);
                //Separate Session ID from UserID
                $sessioncookie = explode('||', $decrypted);
                $sessionid = $sessioncookie[0];
                //Check Session Table
                try {
                    $session = Doctrine_Core::getTable('Model_Session')->findOneBy(self::$_sessionidfield, $sessionid);
                }
                catch (Doctrine_Exception $e) {
                    throw new Zend_Auth_Storage_Exception();
                }
                $session->{self::$_accessedfield} = time();
                try {
                    $session->save();
                }
                catch (Doctrine_Exception $e) {
                    throw new Zend_Auth_Storage_Exception();
                }
               self::$_dbrow = $session;
               return unserialize(self::$_dbrow->{self::$_datafield});
           } 
       }

       /**
        * Writes $contents to Storage
        * 
        * @param mixed $contents
        * @throws Zend_Auth_Storage_Exception If writing $contents is not completed
        * @return bool
        */
        public function write($contents) {
            $requestObject = new Zend_Controller_Request_Http();
            if($cookie = $requestObject->getCookie(self::$_cookieName, FALSE)) {
                //Decrypt Cookie
                $encryption = new Cryptography_EncryptionService('1111834');
                $decrypted = $encryption->decrypt($cookie);
                //Separate Session ID from UserID
                $sessioncookie = explode('||', $decrypted);
                $sessionid = $sessioncookie[0];
                //Check Session Table
                try {
                    $session = Doctrine_Core::getTable('Model_Session')->findOneBy(self::$_sessionidfield, $sessionid);
                }
                catch (Doctrine_Exception $e) {
                    throw new Zend_Auth_Storage_Exception();
                }
                $session->{self::$_accessedfield} = time();
                $session->{self::$_datafield} = serialize($contents->toArray());
                try {
                    $session->save();
                }
                catch (Doctrine_Exception $e) {
                    throw new Zend_Auth_Storage_Exception();
                }
                return true;
            }
            else {
                $session = new Model_Session();
                $session->{self::$_accessedfield} = time();
                $session->{self::$_useridfield} = $contents->id;
                $session->{self::$_hostnamefield} = $_SERVER['REMOTE_ADDR'];
                $session->{self::$_datafield} = serialize($contents);
                try {
                    $session->save();
                } 
                catch (Doctrine_Exception $e) {
                    throw new Zend_Auth_Storage_Exception();
                }
                $encryption = new Cryptography_EncryptionService('1111834');
                $hashing = new Cryptography_HashingService();
                $cookievalue = $session->id.'||'.$hashing->Compute($session->{self::$_hostnamefield});
                if(setcookie(self::$_cookieName, $encryption->encrypt($cookievalue), 0, '/')) return true;
                else throw new Zend_Auth_Storage_Exception();
            } 
        }

        /**
         * Clears contents from storage
         * 
         * @throws Zend_Auth_Storage_Exception If clearing contents is not successful
         * @return bool
         */
         public function clear() {
            $requestObject = new Zend_Controller_Request_Http();
            if($cookie = $requestObject->getCookie(self::$_cookieName, FALSE)) {
                //Decrypt Cookie
                $encryption = new Cryptography_EncryptionService('1111834');
                $decrypted = $encryption->decrypt($cookie);
                //Separate Session ID from UserID
                $sessioncookie = explode('||', $decrypted);
                $sessionid = $sessioncookie[0];
                $query = Doctrine_Query::create()->delete('Model_Session')->where('id = ?', $sessionid);
                try {
                    $query->execute();
                }
                catch (Doctrine_Exception $e) {
                    throw new Zend_Auth_Storage_Exception();
                }
                setcookie(self::$_cookieName, "", time() - 3600, '/');
                return true;
            }
            else {
                throw new Zend_Auth_Storage_Exception();
            }
         }
 }
