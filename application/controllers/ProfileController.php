<?php

class ProfileController extends Base_RestrictedController {
    
    public function init() {
        parent::init();
    }
    
    public function indexAction() {
        $profileService = new Service_Profile();
        if($this->getRequest()->getParam('user', FALSE)) {
            //Implement Find User by Username
        }
        else {
            $this->view->profile = $profileService->fetchProfile($this->_user->id);
            $messageService = new Service_Message();
            $this->view->newMessages = $messageService->messageCount($this->_user->id);
            $this->_helper->layout->setLayout('topmenu');
            return $this->render();
        }
    }
    
    public function loadaboutAction() {
        $profileService = new Service_Profile();
        if($this->_ajaxRequest) {
            if($values = $profileService->fetchProfile($this->_user->id)) {
                $this->_response->appendBody(Zend_Json::encode($values));
                return;
            }
            else {
                $this->_response->appendBody('0');
                return;
            }
        }
        
    }
    
    public function changeprofileimgAction() {
        if($this->getRequest()->isPost()) {
            if(!empty($_FILES['photo']['name'])) {
                $adapter = new Zend_File_Transfer_Adapter_Http();
                $adapter->setDestination(Zend_Registry::get('profileImagesPath'));
                $files = $adapter->getFileInfo();
                $i = 1;
                foreach($files as $file => $info) {
                    if(!$adapter->isUploaded($file)) {
                        return $this->_redirect('/profile');
                    }
                    $extension = strtolower(end(explode('.', $info['name'])));
                    $name = time().$this->_user->id.$i++.".".$extension;
                    $adapter->addFilter('Rename', array('target' => Zend_Registry::get('profileImagesPath').$name, 'overwrite' => TRUE));
                    if(!$adapter->receive($info['name'])) {
                        $this->view->error = 'There was a problem uploading the photo. Please try again later';
                        return $this->render('error');
                    }
                }
                $filename = $adapter->getFileName();
                $filename = basename($filename);
                $changes = array('photo'=>$filename);
                $profileService = new Service_Profile();
                if(($edited = $profileService->editProfile($this->_user->profileid, $changes))) {
                    return $this->_redirect('/profile');
                }
                else {
                    $this->view->error = 'There was a problem updating your profile. Please try again later';
                    return $this->render('error');
                }
            }
        }
        else $this->_redirect('/profile'); 
    }

    public function editprofileAction() {
        if($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            if(empty($values)) {
                if($this->_ajaxRequest) {
                    $this->_response->appendBody('0');
                    return;
                }
                else {
                    return $this->_redirect('/profile');
                }
            }
            $profileService = new Service_Profile();
            if($profileService->editProfile($this->_user->profileid, $values)) {
                if($this->_ajaxRequest) {
                    $this->_response->appendBody('1');
                    return;
                }
                else {
                    return $this->_redirect('/profile');
                }
            }
            else {
                if($this->_ajaxRequest) {
                    $this->_response->appendBody('0');
                    return;
                }
                else {
                    return $this->_redirect('/profile');
                }
            }
        }
    }

    public function loadtownhallsAction() {
        $topicService = new Service_Topic();
        $articleService = new Service_Article();
        $request = array();
        if(is_array($townhalls = $topicService->fetchFollowed($this->_user->id))) $request['townhalls'] = $townhalls;
        if(is_array($articles = $articleService->fetchBookmarked($this->_user->id))) $request['articles'] = $articles;
        if($this->_ajaxRequest) {
            $this->_response->appendBody(Zend_Json::encode($request));
            return;
        }
        else {
            return $this->_redirect('/profile');
        }
    }
    
    public function removetownhallAction() {
        $topicService = new Service_Topic();
        if($this->getRequest()->isPost() && $this->_ajaxRequest) {
            if($topic = $this->getRequest()->getParam('townhall', FALSE)) {
                if($topicService->unfollowTopic($this->_user->id, $topic)) {
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
        else return $this->_redirect('/profile');
    }
    
    public function followtownhallAction() {
        $topicService = new Service_Topic();
        if($this->getRequest()->isPost() && $this->_ajaxRequest) {
            if($topic = $this->getRequest()->getParam('townhall', FALSE)) {
                if($topicService->followTopic($this->_user->id, $topic)) {
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
        else return $this->_redirect('/profile');
    }
    
    public function removearticlebookmarkAction() {
        $articleService = new Service_Article();
        if($this->getRequest()->isPost() && $this->_ajaxRequest) {
            if($article = $this->getRequest()->getParam('article', FALSE)) {
                if($articleService->unfollowArticle($this->_user->id, $article)) {
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
        else return $this->_redirect('/profile');
    }
    
    public function sharearticleAction() {
        if($this->getRequest()->isGet()) {
            if($article = $this->getRequest()->getParam('article', FALSE)) {
                $articleService = new Service_Article();
                $this->view->article = $articleService->fetchOne($article);
                return $this->render();
            }
            else return $this->render('shareerror');
        }
    }
    
    public function loadstoriesAction() {
        if($this->getRequest()->isGet() && $this->_ajaxRequest) {
            $storyService = new Service_Userstory();
            if(is_array($stories = $storyService->fetch($this->_user->id))) {
                $result['root'] = $stories;
                $this->_response->appendBody(Zend_Json::encode($result));
                return;
            }
            else {
                $this->_response->appendBody('0');
                return;
            }
        }
        else return $this->_redirect('/profile');
    }
    
    public function uploadstoryphotosAction() {
        if($this->getRequest()->isPost()) {
            
        }
    }
    
    public function addstoryAction() {
        if($this->getRequest()->isPost() & $this->_ajaxRequest) {
            if($content = $this->getRequest()->getParam('storyContent', FALSE)) {
                
            }
        }
    }
    
    public function addstorycommentAction() {
        if($this->getRequest()->isPost() && $this->_ajaxRequest) {
            if($comment = $this->getRequest()->getParam('comment', FALSE) && $story = $this->getRequest()->getParam('story', FALSE)) {
                $storyService = new Service_Userstory();
                if(is_array($newComment = $storyService->addNewComment($this->_user->id, $story, $comment))) {
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
        else return $this->_redirect('/profile');
    }
    
    public function deletestorycommentAction() {
        if($this->getRequest()->isPost() && $this->_ajaxRequest) {
            if($commentid = $this->getRequest()->getParam('comment', FALSE)) {
                $storyService = new Service_Userstory();
                if($storyService->deleteStoryComment($commentid, $this->_user->id)) {
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
        else return $this->_redirect('/profile');
    }
    
    public function loadmorestoriesAction() {
        if($this->getRequest()->isGet() && $this->_ajaxRequest) {
            $storyService = new Service_Userstory();
            if($page = $this->getRequest()->getParam('page', FALSE)) {
                if(is_array($stories = $storyService->fetch($this->_user->id, $page))) {
                    $this->_response->appendBody(Zend_Json::encode($stories));
                    return;
                }
                else {
                    $this->_response->appendBody('0');
                    return;
                }
            }
        }
        else return $this->_redirect('/profile');
    }
    
    public function loadresourcesAction() {
        if($this->getRequest()->isGet() && $this->_ajaxRequest) {
            $resourceService = new Service_Resource();
            if(is_array($resources = $resourceService->fetchBookmarked($this->_user->id))) {
                $result['root'] = $resources;
                $this->_response->appendBody(Zend_Json::encode($result));
                return;
            }
            else {
                $this->_response->appendBody('0');
                return;
            }
        }
        else return $this->_redirect('/profile');
    }
    
    public function removeresourcebookmarkAction() {
        if($this->getRequest()->isPost() && $this->_ajaxRequest) {
            if($resource =  $this->getRequest()->getParam('resource', FALSE)) {
                $resourceService = new Service_Resource();
                if($resourceService->removeBookmark($resource, $this->_user->id)) {
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
    }
    
    public function loadfriendsAction() {
        if($this->getRequest()->isGet() && $this->_ajaxRequest) {
            $friendService = new Service_Friend();
            if(is_array($friends = $friendService->fetchFriends($this->_user->id))) {
                $result['root'] = $friends;
                $this->_response->appendBody(Zend_Json::encode($result));
                return;
            }
            else {
                $this->_response->appendBody('0');
                return;
            }
        }
        else return $this->_redirect('/profile');
    }
    
}
