<?php

class MessagesController extends Base_RestrictedController {
    
    public function init() {
        parent::init();
    }
    
    public function indexAction() {
        $this->_helper->layout->setLayout('topmenu');
        return $this->render();
    }
    
    public function inboxAction() {
        $messageService = new Service_Message();
        if(is_array($messages = $messageService->fetchReceived($this->_user->id))) {
            $this->view->messages = $messages;
            return $this->render();
        }
        else {
            $this->view->error = "We apologize. Your messages could not be loaded at this time";
            return $this->render();
        }
    }
    
    public function loadinboxAction() {
        if($this->getRequest()->isGet() && $this->_ajaxRequest) {
            $messageService = new Service_Message();
            if(is_array($messages = $messageService->fetchReceived($this->_user->id))) {
                $result['root'] = $messages;
                $this->_response->appendBody(Zend_Json::encode($result));
                return;
            }
            else {
                $this->_response->appendBody('0');
                return;
            }
        }
        else return $this->_redirect('/messages');
    }
    
    public function loadsentAction() {
        if($this->getRequest()->isGet() && $this->_ajaxRequest) {
            $messageService = new Service_Message();
            if(is_array($messages = $messageService->fetchSent($this->_user->id))) {
                $result['root'] = $messages;
                $this->_response->appendBody(Zend_Json::encode($result));
                return;
            }
            else {
                $this->_response->appendBody('0');
                return;
            }
        }
        else return $this->_redirect('/messages');
    }
    
    public function loadreceivedrequestsAction() {
        if($this->getRequest()->isGet() && $this->_ajaxRequest) {
            $friendService = new Service_Friend();
            if(is_array($requests = $friendService->fetchReceivedRequests($this->_user->id))) {
                $result['root'] = $requests;
                $this->_response->appendBody(Zend_Json::encode($result));
                return;
            }
            else {
                $this->_response->appendBody('0');
                return;
            }
        }
        else return $this->_redirect('/messages');
    }
    
    public function loadsentrequestsAction() {
        if($this->getRequest()->isGet() && $this->_ajaxRequest) {
            $friendService = new Service_Friend();
            if(is_array($requests = $friendService->fetchSentRequests($this->_user->id))) {
                $result['root'] = $requests;
                $this->_response->appendBody(Zend_Json::encode($result));
                return;
            }
            else {
                $this->_response->appendBody('0');
                return;
            }
        }
        else return $this->_redirect('/messages');
    }
    
    public function countnewmessagesAction() {
        if($this->getRequest()->isGet() && $this->_ajaxRequest) {
            $messageService = new Service_Message();
            $friendService = new Service_Friend();
            $this->_response->appendBody($messageService->messageCount($this->_user->id));
        }
    }
    
    public function loadmessageAction() {
        if($this->getRequest()->isGet() && $this->_ajaxRequest) {
            if($mid = $this->getRequest()->getParam('mid', FALSE)) {
                $messageService = new Service_Message();
                if(is_array($message = $messageService->fetchMessage($mid, $this->_user->id))) {
                    $messageService->read($mid, $this->_user->id, 1);
                    $this->_response->appendBody(Zend_Json::encode($message));
                    return;
                }
                else {
                    $this->_response->appendBody('0');
                    return;
                }
            }
            else {
                $this->_response->appendBody('0');
                return;
            }
        }
        else return $this->_redirect('/messages');
    }
    
    public function sendAction() {
        if($this->getRequest()->isPost() && $this->_ajaxRequest) {
            $values = $this->getRequest()->getPost();
            if($values['subject'] == "" || count($values['recipients']) == 0 || $values['content'] == "") {
                $this->_response->appendBody(Zend_Json::encode(array('result'=>'missing value')));
                return;
            }
            $recipients = array();
            foreach($values['recipients'] as $rec) {
                $recipients[] = $rec['id'];
            }
            if(count($recipients) == 1) 
                $recipients = $recipients[0];
            $messageService = new Service_Message();
            if(is_array($result = $messageService->addNew($this->_user->id, $recipients, $values['type'], ($values['ref'] == "")?NULL:$values['ref'], $values['subject'], $values['content']))) {
                $this->_response->appendBody(Zend_Json::encode($result));
                return;
            }
            else {
                $this->_response->appendBody(Zend_Json::encode(array('result'=>$result)));
                return;
            }
        }
    }

    public function togglereadAction() {
        if($this->getRequest()->isPost() && $this->_ajaxRequest) {
            if(($message = $this->getRequest()->getParam('mid', FALSE))) {
                $newStatus = $this->getRequest()->getParam('status');
                $messageService = new Service_Message();
                if($result = $messageService->toggleRead($message, $this->_user->id, $newStatus)) {
                    $this->_response->appendBody('1');
                    return;
                }
                else {
                    $this->_response->appendBody('0');
                    return;
                }
            }
            else {
                $this->_response->appendBody('0');
                return;
            }
        }
        else 
            return $this->_redirect("/messages");
    }
    
    public function deletemsgAction() {
        if($this->getRequest()->isPost() && $this->_ajaxRequest) {
            if($mid = $this->getRequest()->getParam('mid', FALSE)) {
                $messageService = new Service_Message();
                if($messageService->deleteMessage($mid, $this->_user->id)) {
                    $this->_response->appendBody('1');
                    return;
                }
                else {
                    $this->_response->appendBody('0');
                    return;
                }
            }
            else {
                $this->_response->appendBody('0');
                return;
            }
        }
        else
            return $this->_redirect("/messages");
    }
}
