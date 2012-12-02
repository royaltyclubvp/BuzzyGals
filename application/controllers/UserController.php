<?php

class UserController extends Base_FoundationController {
    
    protected $service = null;
    
    public function init() {
        parent::init();
        $this->service = new Service_User();
    }
    
    /**
     * Check Existence of User by Username
     * 
     */
    public function checkuserAction() {
        if($this->getRequest()->isPost() && $this->_ajaxRequest) {
            if($username = $this->getRequest()->getParam('username', FALSE)) {
                $result = $this->service->exists('username', $username);
                if($result) {
                    $this->_response->appendBody('1');
                    return;
                }
                else {
                    $this->_response->appendBody('0');
                    return;
                } 
            }
            else {
                $this->_response->appendBody('-1');
                return;
            }
        }
    }
    
    /**
     * Display Registration Page with Previously Chosen Values
     */
    public function signupAction() {
        $this->_helper->layout->setLayout('single');
        return $this->render();
    }
    
    /**
     * Process User Registration Attempt
     */
    public function registerAction() {
        if($this->getRequest()->isPost() && $this->_ajaxRequest) {
            $values = $this->getRequest()->getPost();
            $errors = array();
            $i = 0;
            if($this->service->exists('username', $values['username'])) {
                $errors['root'][$i]['name'] = 'username';
                $errors['root'][$i++]['text'] = 'Username exists';
                $this->_response->appendBody(Zend_Json::encode($errors));
                return;
            }
            //Validate Values
            if(!Zend_Validate::is($values['username'], 'EmailAddress')) {
                $errors['root'][$i]['name'] = 'username';
                $errors['root'][$i++]['text'] = 'Invalid E-Mail Format';
            }
            if(!Zend_Validate::is($values['password'], 'StringLength', array(array('min' => 7, 'max' => 25)))) {
                $errors['root'][$i]['name'] = 'password';
                $errors['root'][$i++]['text'] = 'Should be 7-25 characters';
            }
            if(!Zend_Validate::is($values['passwordConfirm'], 'Identical', array($values['password']))) {
                $errors['root'][$i]['name'] = 'password';
                $errors['root'][$i++]['text'] = 'Passwords do not match';
            }
            if(!Zend_Validate::is($values['displayName'], 'Alnum', array(TRUE)) || !Zend_Validate::is($values['displayName'], 'StringLength', array(array('min' => 5, 'max' => 30)))) {
                $errors['root'][$i]['name'] = 'displayName';
                $errors['root'][$i++]['text'] = 'Should be Alpanumeric. 5-30 characters';
            }
            if(!checkdate($values['dob_month'], $values['dob_day'], $values['dob_year'])) {
                $errors['root'][$i]['name'] = 'dob';
                $errors['root'][$i++]['text'] = 'DOB is invalid';
            }
            
            if($i) {
                $this->_response->appendBody(Zend_Json::encode($errors));
                return;
            }
            
            $values['dob'] = $values['dob_year'].'-'.$values['dob_month'].'-'.$values['dob_day'];
            //Assign Username to Email Field
            $values['email'] = $values['username'];
            //Create Verification String
            $values['verificationcode'] = md5(uniqid(rand(), TRUE)).time();
            $locationService = new Service_Location();
            if($location = $locationService->locationExists($values['cityid'], $values['stateprovid'], $values['countryid'])) {
                $values['location'] = $location;
            }
            else {
                $locationArray = array(
                    'cityid' => $values['cityid'],
                    'stateprovid' => $values['stateprovid'],
                    'countryid' => $values['countryid'],
                    'country' => $values['country'],
                    'stateprov' => $values['stateprov'],
                    'city' => $values['city']
                );
                if($location = $locationService->addLocation($locationArray)) {
                    $values['location'] = $location;
                }
                else {
                    $values['location'] = 1;
                }
            }
            //Set UserGroup
            $values['usergroup'] = 1;
            if(is_array($newAccount = $this->service->addNew($values))) {
                $from = Zend_Registry::get('registrationEmail');
                $to = $newAccount['username'];
                $subject = "Welcome to BuzzyGals: Please Verify Your Account Registration";
                $verificationLink = "http://buzzygals.com/account/verify/".$newAccount['verificationcode']."/".$newAccount['id'];
                $body = $verificationLink;
                $this->sendEmail($from, $to, $subject, $body);
            }
            $response['root'] = $newAccount;
            $this->_response->appendBody(Zend_Json::encode($response));
            return;
        }
        else {
            return $this->_redirect('/profile');
        }
    }
    
    /**
     * Verify User Account
     */
    public function verifyAction() {
         if($userid = $this->getRequest()->getParam('u', FALSE) && $verifystring = $this->getRequest()->getParam('v', FALSE)) {
             $user = $this->service->getUser('id', $userid);
             if($user->verificationcode != $verifystring) {
                 //Check if Verification Code is In Record
                 if($user->verificationcode == NULL && $user->verified == 1) {
                     $this->view->error = 'This account has already been verified. Please click <a href="/login">here</a> to login to your account.';
                     return $this->render('verificationerror');
                 }
                 else if($user->verificationcode != NULL) {
                     $this->view->error = 'The URL did not validate against our records. Please check your e-mail again to ensure that the link is correct.';
                     return $this->render('verificationerror');
                 }
             }
             $result = $this->service->verifyAccount($userid);
             if(!$result) {
                 $this->view->error = 'There was an error in the verification process. Please try again later';
                 return $this->render('verificationerror');
             }
             return $this->render('verificationsuccess');
         }
     }
    
    /**
     * Login User
     */
    public function loginAction() {
        $this->_helper->layout->setLayout('single');
        if($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            if($values['username'] != '' && $values['password'] != '') {
                //Set Auth_Adapter Configuration
                $adapterConfig = array(
                    'className' => 'Model_User',
                    'identityVar' => 'username',
                    'credentialVar' => 'password'
                );
                $adapter = new Auth_Adapter_DoctrineTable($adapterConfig);
                $adapter->setCredentials(trim($values['username']), trim($values['password']));
                $storage = new Auth_Adapter_AuthDoctrineDbStorage('auth');
                $auth = Zend_Auth::getInstance();
                $auth->setStorage($storage);
                $result = $auth->authenticate($adapter);
                if($result->isValid()) {
                    if(isset($values['keepLoggedIn'])) {
                        $encryption = new Cryptography_EncryptionService('12675');
                        $cookievalue = trim($values['username']).'||'.trim($values['password']);
                        $cookievalue = $encryption->encrypt($cookievalue);
                        setcookie('authPersistence',$cookievalue, time() + 2592000, '/');
                    }
                    return $this->_redirect('/profile');
                }
                else {
                    $this->view->error = $result->getMessages();
                    return $this->render('login');
                }
            }
            else {
                $errors = array('Please enter both a username and a password');
                $this->view->error = $errors;
                return $this->render('login');
            }
        }
        else {
            if($cookie = $this->getRequest()->getCookie('authPersistence', FALSE)) {
                $encryption = new Cryptography_EncryptionService('12675');
                $cookie = $encryption->decrypt($cookie);
                $credentials = explode('||', $cookie);
                $adapterConfig = array(
                    'className' => 'Model_User',
                    'identityVar' => 'username',
                    'credentialVar' => 'password'
                );
                $adapter = new Auth_Adapter_DoctrineTable($adapterConfig);
                $storage = new Auth_Adapter_AuthDoctrineDbStorage('auth');
                $auth = Zend_Auth::getInstance();
                $auth->setStorage($storage);
                $result = $auth->authenticate($adapter);
                if($result->isValid()) return $this->_redirect('/profile');
                else {
                    $this->view->error = $result->getMessages();
                    return $this->render('login');
                }
            }
            else {
                return $this->render('login');
            } 
        }
    }

    public function logoutAction() {
        $auth = Zend_Auth::getInstance();
        $storage = new Auth_Adapter_AuthDoctrineDbStorage('auth');
        $auth->setStorage($storage);
        $auth->clearIdentity();
        if(isset($COOKIE['authPersistence'])) setcookie('authPersistence',"", time() - 3600, '/');
        return $this->_redirect('/');
    }
    
    public function getlocationsAction() {
        if($this->getRequest()->isGet() && $this->_ajaxRequest) {
            $locationService = new Service_Location();
            if(is_array($locations = $locationService->getLocationList('country'))) {
                $this->_response->appendBody(Zend_Json::encode($locations));
                return;
            }
            else {
                $this->_response->appendBody('0');
                return;
            }        
        }
        else {
            return $this->_redirect('/profile');
        }
    }
}
