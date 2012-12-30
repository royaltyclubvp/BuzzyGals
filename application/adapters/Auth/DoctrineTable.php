<?php

/**
 * Doctrine Zend_Auth Adapter [Doctrine Version 1.2.4]
 * 
 * @package     ElleFab
 * @subpackage  Zend_Auth
 * @version     1
 * @author      Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 */
 
 class Auth_Adapter_DoctrineTable implements Zend_Auth_Adapter_Interface {
    protected $_className = null;
    
    protected $_identityVar = null;
    
    protected $_credentialVar = null;
    
    protected $_resultInfo = null;
    
    protected $_identity = null;
    
    protected $_credentials = null;
    
    public function __construct($config) {
        //Set Class Variables
        $this->_className = $config['className'];
        $this->_identityVar = $config['identityVar'];
        $this->_credentialVar = $config['credentialVar'];
        
        //Initialise Response Array
        $this->_resultInfo['code'] = $this->_resultInfo['identity'] = null;
        $this->_resultInfo['messages'] = array();
    }
    
    protected function _createAuthResult() {
        return new Zend_Auth_Result($this->_resultInfo['code'], $this->_resultInfo['identity'], $this->_resultInfo['messages']);
    }
    
    public function setCredentials($identity, $credentials) {
        $this->_identity = $identity;
        $this->_credentials = $credentials;
    }
    
    public function authenticate() {
        $user = Doctrine_Core::getTable($this->_className)->findOneBy($this->_identityVar, $this->_identity);
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
        if($user->enabled) {
            
            //Check Credentials
            $hashing = new Cryptography_PasswordService();
            if ($hashing->verify($this->_credentials, $user->password)) {
                $userService = new Service_User();
                if($profile = $userService->getUserProfile($user->id)) {
                    $user->mapValue('profileid', $profile->id);
                    $user->mapValue('locationid', $profile->location);
                    $user->mapValue('cityid', $profile->Location->cityid);
                    $user->mapValue('stateprovid', $profile->Location->stateprovid);
                    $user->mapValue('countryid', $profile->Location->countryid);
                }
                else $this->_resultInfo['messages'][] = "Could not load User Profile";
                $user['password'] = '';
                $this->_resultInfo['identity'] = $user;
                $this->_resultInfo['code'] = Zend_Auth_Result::SUCCESS;
                $this->_resultInfo['messages'][] = "Login was successful";
                $userService->registerLogin($user->id);
                return $this->_createAuthResult();
            }
            else {
                $this->_resultInfo['code'] = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
                $this->_resultInfo['identity'] = $this->_identity;
                $this->_resultInfo['messages'][] = "The password supplied is not correct";
                $userService = new Service_User();
                if($userService->registerFailure($user->id) > Zend_Registry::get('maxAccessAttempts')) {
                    $userService->disableAccount($user->id);
                    $this->_resultInfo['messages'][] = "The account has been locked due to multiple failed access attempts";
                }
                return $this->_createAuthResult();
            }
        }
        else {
            $this->_resultInfo['code'] = Zend_Auth_Result::FAILURE;
            $this->_resultInfo['messages'][] = "That account has been disabled";
            return $this->_createAuthResult();
        }
    }
    
    
 }
