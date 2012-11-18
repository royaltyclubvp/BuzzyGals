<?php

class TownhallsController extends Base_RestrictedController {
    
    public function init() {
        parent::init();
    }
    
    public function indexAction() {
        $this->_helper->layout->setLayout('topmenu');
        $topicService = new Service_Topic();
        if(is_array($townhalls = $topicService->fetchUrlList())) {
            if($name = $this->getRequest()->getParam('name', FALSE)) {
                if($topic = array_search($name, $townhalls)) {
                    $resourceService = new Service_Resource();
                    $articleService = new Service_Article();
                    $newestArticles = $articleService->fetchNewest($topic);
                    $this->view->newest = $newestArticles;
                    $this->view->popular = $articleService->fetchPopular($topic);
                    $this->view->title = ucfirst($name);
                    $this->view->resourceCount = $resourceService->fetchByTopic($topic, TRUE);
                    $this->view->topic = $topic;
                    return $this->render();
                }
            }
            return $this->_redirect("/townhalls/home");
        }
        else {
            return $this->_redirect("/error"); //Modify for Error Response Page
        }
    }

    public function loadmorenewestAction() {
        if($this->getRequest()->isGet() && $this->_ajaxRequest) {
            $topic = $this->getRequest()->getParam('topic');
            $page = $this->getRequest()->getParam('page');
            if($topic && $page) {
                $articleService = new Service_Article();
                if(is_array($results = $articleService->fetchNewest($topic, $page, ($this->getRequest()->getParam('noPerPage', FALSE)) ? $this->getRequest()->getParam('noPerPage') : 5))) {
                    $this->_response->appendBody(Zend_Json::encode($results));
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

    public function homeAction() {
        $this->_helper->layout->setLayout('topmenu');
        $topicService = new Service_Topic();
        $this->view->topics = $topicService->fetchAll();
        return $this->render();
    }
    
    public function articleAction() {
        if($uri = $this->getRequest()->getParam('uri', FALSE)) {
            $articleService = new Service_Article();
            if($article = $articleService->fetchOneByUri($uri)) {
                $this->_helper->layout->setLayout('topmenu');
                $this->view->article = $article;
                return $this->render();
            }
            else {
                return $this->render();
            }
        }
        else {
            return $this->render();
        }
    }
}
