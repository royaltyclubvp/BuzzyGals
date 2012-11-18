<?php

abstract class Base_RestrictedController extends Base_FoundationController {
    
    protected $_user = null;
    
    public function init() {
        parent::init();
        
        //Check Authentication State. Redirect If Not Logged In
        $authentication = Zend_Auth::getInstance();
        $storage = new Auth_Adapter_AuthDoctrineDbStorage('auth');
        $authentication->setStorage($storage);
        if($authentication->hasIdentity()) {
            $this->_user = $authentication->getIdentity();
        }
        else {
            $controller = $this->getRequest()->getControllerName();
            $action = $this->getRequest()->getActionName();
            return $this->_redirect("/user/login?returnc=$controller&returna=$action", array("exit" => TRUE));
        }
    }
   
}
