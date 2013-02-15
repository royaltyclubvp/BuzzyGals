<?php

/**
 * Doctrine Zend_Auth Adapter [Doctrine Version 1.2.4]
 * 
 * @package     BuzzyGals
 * @subpackage  Zend_Auth
 * @version     2
 * @author      Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 */
 
 class Auth_Adapter_DoctrineTable implements Zend_Auth_Adapter_Interface {
    
    protected $_resultInfo = null;
    
    protected $_username = null;
    
    protected $_password = null;
    
    public function __construct() {
        //Initialise Response Array
        $this->_resultInfo['code'] = $this->_resultInfo['identity'] = null;
        $this->_resultInfo['messages'] = array();
    }
    
    protected function _createAuthResult() {
        return new Zend_Auth_Result($this->_resultInfo['code'], $this->_resultInfo['identity'], $this->_resultInfo['messages']);
    }
    
    public function setCredentials($username, $password) {
        $this->_username = $username;
        $this->_password = $password;
    }
    
    public function authenticate() {
        $user = Doctrine_Core::getTable('Model_User')->findOneBy('username', $this->_username);
        
        //Check If Account Was Found
        if(!$user) {
            $this->_resultInfo['code'] = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
            $this->_resultInfo['messages'][] = 'No account with that username could be located';
            return $this->_createAuthResult();
        }
        
        //Check If Account Has Been Verified
        if(!$user->verified) {
            $this->_resultInfo['code'] = Zend_Auth_Result::FAILURE;
            $this->_resultInfo['messages'][] = 'This account has not been verified. Please check your e-mail for instructions on completing this process';
            return $this->_createAuthResult();
        }
        
        //Check If Account Is Enabled
        if(!$user->enabled) {
            $this->_resultInfo['code'] = Zend_Auth_Result::FAILURE;
            $this->_resultInfo['messages'][] = "Account has been disabled";
            return $this->_createAuthResult();
        }
        
        //Compare Password
        $bcrypt = new Cryptography_PasswordService();
        /*if($user->password =! ($test = $bcrypt->verify($this->_password, $user->password))) {
            $this->_resultInfo['code'] = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
            $this->_resultInfo['identity'] = $this->_username;
            $this->_resultInfo['messages'][] = $test."    ".$user->password;
            return $this->_createAuthResult();
        }*/
        if(!$bcrypt->verify($this->_password, $user->password)) {
            $this->_resultInfo['code'] = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
            $this->_resultInfo['identity'] = $this->_username;
            $this->_resultInfo['messages'][] = "Login Failed. Password Incorrect.";
            $userService = new Service_User();
            if($userService->registerFailure($user->id) > Zend_Registry::get('maxAccessAttempts')) {
                $userService->disableAccount($user->id);
                $this->_resultInfo['messages'][] = "This account has been locked due to multiple failed access attempts";
            }
            return $this->_createAuthResult();
        }
        
        //Set Login Info
        $userService = new Service_User();
        if($profile = $userService->getUserProfile($user->id)) {
            $user->mapValue('profileid', $profile->id);
            $user->mapValue('locationid', $profile->location);
            $user->mapValue('cityid', $profile->Location->cityid);
            $user->mapValue('stateprovid', $profile->Location->stateprovid);
            $user->mapValue('countryid', $profile->Location->countryid);
        }
        $user['password'] = "";
        $this->_resultInfo['identity'] = $user;
        $this->_resultInfo['code'] = Zend_Auth_Result::SUCCESS;
        $this->_resultInfo['messages'][] = "Login was successful";
        $userService->registerLogin($user->id);
        return $this->_createAuthResult();
       
    }
    
    
 }
