<?php

class ResourcesController extends Base_RestrictedController {
    
    public function init() {
        parent::init();
    }
    
    public function indexAction() {
        //Load Townhall Resource Page
        if($topicName = $this->getRequest()->getParam('topic', FALSE)) {
            $topicService = new Service_Topic();
            if(is_array($topics = $topicService->fetchUrlList())) {
                if($topic = array_search($topicName, $topics)) {
                    $this->_helper->layout->setLayout('topmenu');
                    $resourceService = new Service_Resource();
                    if(is_array($resources = $resourceService->fetchByTopic($topic))) {
                        $this->view->resources = $resources['resources'];
                        $this->view->resourceCount = $resources['total'];
                        return $this->render('topicResources');
                    }
                    else {
                        return $this->_redirect("/resources/error");
                    }
                }
                else 
                    return $this->_redirect("/resources/error");
            }
            else 
                return $this->_redirect("/error"); //Modify for Error Response Page
        }
        //Load Resources By Latest & By Location
        else {
            $this->_helper->layout->setLayout('topmenu');
            $resourceService = new Service_Resource();
            $this->view->newestResources = $resourceService->fetchNewest();
            $this->view->locationResources = $resourceService->fetchByCity($this->_user->cityid);
            return $this->render();
        }
    }
    
    public function bookmarkAction() {
        if($this->getRequest()->isPost() && $this->_ajaxRequest) {
            if($resource = $this->getRequest()->getParam('resource', FALSE)) {
                $resourceService = new Service_Resource();
                if(is_array($results = $resourceService->addBookmark($resource, $this->_user->id))) {
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
        else {
            $this->_response->appendBody('0');
            return;
        }
    }
    
    //Remove Resource Function Located in Profile Controller. Removed from this location to eliminate unnecessary Code Duplication
}
