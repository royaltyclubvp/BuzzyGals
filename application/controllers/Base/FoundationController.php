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
     * 
     * @param   string      $from       Sender Address
     * @param   string      $fromName   Sender Name
     * @param   array       $recipients Recipient Array
     * @param   string      $subject    Message Subject
     * @param   string      $body       Message Boy
     * @return  bool
     */
    protected function sendEmail($from, $fromName, $recipients, $subject, $body) {
        $config = array(
            'auth' => 'login',
            'username' => 'registration_local+buzzygals.com',
            'password' => 'FDDn89$$'
        );
        $transport = new Zend_Mail_Transport_Smtp('mail.buzzygals.com',$config);
        Zend_Mail::setDefaultTransport($transport);
        $mail = new Zend_Mail();
        $mail->setBodyHTML($body)
            ->setFrom($from, $fromName)
            ->setSubject($subject);
        foreach($recipients as $recipient) {
            $mail->addTo($recipient['email'], $recipient['name']);
        }
        $mail->send();
        return true;
    }
}
