<?php

class ResourcesController extends Base_RestrictedController {
    
    public function init() {
        parent::init();
    }
    
    public function indexAction() {
        //Load Townhall Resource Page
        if($topicName = $this->getRequest()->getParam('topic', FALSE)) {
            $topicName = strtolower($topicName);
            $topicService = new Service_Topic();
            if(is_array($topics = $topicService->fetchUrlList())) {
                if($topic = array_search($topicName, $topics)) {
                    $this->_helper->layout->setLayout('topmenu');
                    $resourceService = new Service_Resource();
                    if(is_array($resources = $resourceService->fetchByTopic($topic))) {
                        for($i=0; $i < $resources['total']; $i++) {
                            $bookmarked = false;
                            if(count($resources['resources'][$i]['Bookmarkers'])) {
                                foreach($resources['resources'][$i]['Bookmarkers'] as $bookmarker) {
                                    if($this->_user->id == $bookmarker['id']) {
                                        $bookmarked = true;
                                        break;
                                    }
                                }
                            }
                            $resources['resources'][$i]['bookmarked'] = $bookmarked;
                        }
                        $this->view->title = ucfirst($topicName);
                        $this->view->resources = $resources['resources'];
                        $this->view->resourceCount = $resources['total'];
                        return $this->render('topicResources');
                    }
                    else {
                        return $this->_redirect("/resources/error");
                    }
                }
                else {
                    $this->view->error = $topics;
                    return $this->render();
                    return $this->_redirect("/resources/error");   
                }
            }
            else 
                return $this->_redirect("/error"); //Modify for Error Response Page
        }
        //Load Resources By Latest & By Location
        else {
            $this->_helper->layout->setLayout('topmenu');
            $resourceService = new Service_Resource();
            $newestResources = $resourceService->fetchNewest();
            for($i=0; $i < count($newestResources); $i++) {
                $bookmarked = false;
                if(count($newestResources[$i]['Bookmarkers'])) {
                    foreach($newestResources[$i]['Bookmarkers'] as $bookmarker) {
                        if($this->_user->id == $bookmarker['id']) {
                            $bookmarked = true;
                            break;
                        }
                    }
                }
                $newestResources[$i]['bookmarked'] = $bookmarked;    
            }
            $this->view->newestResources = $newestResources;
            $locationResources = $resourceService->fetchByState($this->_user->stateprovid);
            for($i=0; $i < $locationResources['total']; $i++) {
                $bookmarked = false;
                if(count($locationResources['resources'][$i]['Bookmarkers'])) {
                    foreach($locationResources['resources'][$i]['Bookmarkers'] as $bookmarker) {
                        if($this->_user->id == $bookmarker['id']) {
                            $bookmarked = true;
                            break;
                        }
                    }
                }
                $locationResources['resources'][$i]['bookmarked'] = $bookmarked;    
            }
            $this->view->localResources = $locationResources;
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
    
    public function searchAction() {
        if($this->getRequest()->isGet()) {
            if($terms = $this->getRequest()->getParam('searchTerms', FALSE)) {
                $resourceService = new Service_Resource();
                if(is_array($results = $resourceService->searchResources($terms))) {
                    $this->_helper->layout->setLayout('topmenu');
                    for($i=0; $i < count($results); $i++) {
                        $bookmarked = false;
                        if(count($results[$i]['Bookmarkers'])) {
                            foreach($results[$i]['Bookmarkers'] as $bookmarker) {
                                if($this->_user->id == $bookmarker['id']) {
                                    $bookmarked = true;
                                    break;
                                }
                            }
                        }
                        $results[$i]['bookmarked'] = $bookmarked;
                    }
                    $result['root'] = $results;
                    $this->view->searchTerms = $terms;
                    $this->view->resultTotal = count($results);
                    $this->view->searchNoPerPage = 10;
                    $this->view->resources = $result;
                    return $this->render('searchresults');
                }
            }
        }
        else $this->render('searchresults');
    }
    //Remove Resource Function Located in Profile Controller. Removed from this location to eliminate unnecessary Code Duplication
}
