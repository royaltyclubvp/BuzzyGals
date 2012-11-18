<?php

class IndexController extends Base_FoundationController {

    public function init() {
        parent::init();
    }

    public function indexAction() {
       //If logged in, direct to profile pagew
       $auth = Zend_Auth::getInstance();
       $storage = new Auth_Adapter_AuthDoctrineDbStorage('auth');
       $auth->setStorage($storage);
       if($auth->hasIdentity()) {
           return $this->_forward('index','profile');
       }
       else {
           $this->_helper->layout->setLayout('single');
           return $this->render();
       }
    }


}
