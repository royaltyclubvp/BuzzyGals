<?php

/**
 *
 * XPDO Zend_Auth Adapter [XPDO 2.1.2]
 *
 * @package ElleFab
 * @subpackage Zend_Auth Adapters
 * @version 1
 * @author Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 */

class Auth_Adapter_XpdoTable implements Zend_Auth_Adapter_Interface {
	
	protected $_className = null;
	
	protected $_identityVar = null;
	
	protected $_credentialVar = null;
	
	protected $_resultInfo = null;
	
	protected $_identity = null;
	
	protected $_credentials = null;
	
	protected $_xpdo = null;
	
	public function __construct($config) {
		
		//Set Class Variables
		$this->_className = $config['className'];
		$this->_identityVar = $config['identityVar'];
		$this->_credentialVar = $config['credentialVar'];
		
		//Initialise Response Array
		$this->_resultInfo['code'] = $this->_resultInfo['identity'] = null;
		$this->_resultInfo['messages'] = array();
		
		//Initialise XPDO Class
		$bootstrap =  $this->getInvokeArg('bootstrap');        
        $this->_xpdo = $bootstrap->getResource('database');
	}
	
	protected function _createAuthResult() {
		return new Zend_Auth_Result($this->_resultInfo['code'], $this->_resultInfo['identity'], $this->_resultInfo['messages']);
	}
	
	public function setCredentials($identity, $credentials) {
		$this->_identity = $identity;
		$this->_credentials = $credentials;
	}
	
	public function authenticate() {
		$user = $this->_xpdo->newObject($this->_className, array($this->_identityVar => $this->_identity));
		if(!is_object($user)) {
			$this->_resultInfo['code'] = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
			$this->_resultInfo['messages'][] = 'No account with that username could be located';
			return $this->_createAuthResult();
		}
		
		//Check If Account Is Enabled
		if($user->status) {
			
			//Check Credentials
			$hashing = new Cryptography_HashingService();
            if ($hashing->Verify($this->_credentials, $user->password)) {
                $userService = new Service_User();
                if($profile = $userService->getUserProfile($user->id)) $user->profile = $profile;
                else $this->_resultInfo['messages'][] = "Could not load User Profile";
                $this->_resultInfo['identity'] = $user;
                $this->_resultInfo['code'] = Zend_Auth_Result::SUCCESS;
                $this->_resultInfo['messages'][] = "Login was successful";
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
            $this->_resultInfo['messages'][] = "That account is not currently enabled. If this is a new account, please check for your confirmation e-mail";
            return $this->_createAuthResult();
        }
	}
}