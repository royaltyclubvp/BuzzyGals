<?php

abstract class Base_FoundationController extends Zend_Controller_Action {
    
    protected $_ajaxRequest = 0;
    
    public function init() {
        
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            //Set AJAX Request Indicator
            $this->_ajaxRequest = 1;
        }
        else {
            $this->_helper->layout->setLayout('layout');
        }
    }
    
    /**
     * Send Email Message
     */
    protected function sendEmail($from, $recipients, $subject, $body) {
        $mail = new Zend_Mail();
        return true;
    }
}
