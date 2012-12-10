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
                    $this->view->resourceCount = $resourceService->fetchByTopic($topic, $this->_user->id, TRUE);
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
        $results = $topicService->fetchAll();
        for($i=0; $i < count($results); $i++) {
            $following = false;
            if(count($results[$i]['Followers'])) {
                foreach($results[$i]['Followers'] as $follower) {
                    if($this->_user->id == $follower['id']) {
                        $following = true;
                        break;
                    }
                }
            }
            $results[$i]['following'] = $following;
        }
        $result['root'] = $results;
        $this->view->townhalls = $result;
        return $this->render();
    }
    
    public function articleAction() {
        if($uri = $this->getRequest()->getParam('uri', FALSE)) {
            $articleService = new Service_Article();
            if($article = $articleService->fetchOneByUri($uri)) {
                $this->_helper->layout->setLayout('topmenu');
                $this->view->menuTitle = $article['Topic']['name'];
                $bookmarked = false;
                if(count($article['Followers'])) {
                    foreach($article['Followers'] as $follower) {
                        if($this->_user->id == $follower['id']) {
                            $bookmarked = true;
                            break;
                        }
                    }
                }
                $this->view->bookmarked = $bookmarked;
                unset($article['Followers']);
                $this->view->article = $article;
                $this->view->userId = $this->_user->id;
                return $this->render();
            }
            else {
                return $this->render('articlenotfound');
            }
        }
        else {
            return $this->_redirect('/townhalls/home');
        }
    }

    public function addarticlecommentAction() {
        if($this->getRequest()->isPost() && $this->_ajaxRequest) {
            if(($article = $this->getRequest()->getParam('article', FALSE)) && ($comment = $this->getRequest()->getParam('comment', FALSE))) {
                $articleService = new Service_Article();
                if($newComment = $articleService->addComment($this->_user->id, $article, $comment)) {
                    $response['root'] = $newComment;
                    $this->_response->appendBody(Zend_Json::encode($response));
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
    
    public function deletestoryownedcommentAction() {
        if($this->getRequest()->isPost() && $this->_ajaxRequest) {
            if(($article = $this->getRequest()->getParam('article', FALSE)) && ($comment = $this->getRequest()->getParam('comment', FALSE))) {
                $articleService = new Service_Article();
                if($result = $articleService->deleteComment($this->_user->id, $comment, $article)) {
                    $this->_response->appendBody($result);
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
}
