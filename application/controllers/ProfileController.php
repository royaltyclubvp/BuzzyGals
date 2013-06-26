<?php

class ProfileController extends Base_RestrictedController {
    
    public function init() {
        parent::init();
    }
    
    public function indexAction() {
        $profileService = new Service_Profile();
        $storyService = new Service_Userstory();
        $friendService = new Service_Friend();
        $userList = $friendService->fetchFriendIds($this->_user->id);
        $this->_helper->layout->setLayout('topmenu');
        $this->view->profile = $profileService->fetchProfile($this->_user->id);
    }
    
    public function friendprofileAction() {
        if($username = $this->getRequest()->getParam('username', FALSE)) {
            $profileService = new Service_Profile();
            if(is_array($profile = $profileService->fetchProfileByUsername($username))) {
                $this->_helper->layout->setLayout('topmenu');
                $this->view->profile = $profile;
                $this->view->userid = $this->_user->id;
                return $this->render();
            }
            else {
                $this->view->error = $profile;
                return $this->render('friendprofilenotfound');
            }
        }
        else {
            return $this->_redirect('/profile');
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

    public function loadfriendaboutAction() {
        if($this->getRequest()->isGet() && $this->_ajaxRequest) {
            if($username = $this->getRequest()->getParam('username', FALSE)) {
                $profileService = new Service_Profile();
                if(is_array($profile = $profileService->fetchProfileByUsername($username))) {
                    $this->_response->appendBody(Zend_Json::encode($profile));
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
    
    public function addarticlebookmarkAction() {
        if($this->getRequest()->isPost() && $this->_ajaxRequest) {
            if($article = $this->getRequest()->getParam('article', FALSE)) {
                $articleService = new Service_Article();
                if(is_array($bookmark = $articleService->followArticle($this->_user->id, $article))) {
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
        return $this->_redirect('/profile');
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
    
    public function loadfriendstoriesAction() {
        if($this->getRequest()->isGet() && $this->_ajaxRequest) {
            if($id = $this->getRequest()->getParam('userId', FALSE)) {
                $storyService = new Service_Userstory();
                if(is_array($stories = $storyService->fetch($id))) {
                    $result['root'] = $stories;
                    $this->_response->appendBody(Zend_Json::encode($result));
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
    
    public function uploadstoryphotosAction() {
        if($this->getRequest()->isPost()) {
            $uploader = new File_Adapter_Uploader('image', array('jpg', 'jpeg'), '5M');
            if(($settings = $uploader->checkServerSettings()) === true) {
                $result = $uploader->handleUpload(Zend_Registry::get('userGalleryImagesPath'), $this->_user->id);
                if(isset($result['filename'])) {
                    $thumbGenerator = new File_Adapter_Image_Filter(Zend_Registry::get('userGalleryThumbsPath'));
                    $thumbGenerator->setConfig(140, 140, 90, 'jpeg');
                    $thumbGenerator->generateThumb(Zend_Registry::get('userGalleryImagesPath').$result['filename']);   
                }
                if($this->_ajaxRequest) {
                    $this->_response->appendBody(Zend_Json::encode($result));
                    return;
                }
                else {
                    $this->view->uploadresponse = $result;
                    return $this->render('uploadstoryphotos');
                }
            }
            else {
                if($this->_ajaxRequest) {
                    $this->_response->appendBody(Zend_Json::encode($settings));
                    return;
                }
                else {
                    $this->view->uploadresponse = $settings;
                    return $this->render('uploadstoryphotos');
                }
            }
        }
        else 
            return $this->_redirect('/profile'); 
    }

    public function uploadprofilephotoAction() {
        if($this->getRequest()->isPost()) {
            $uploader = new File_Adapter_Uploader('image', array('jpg', 'jpeg'), '5M');
            if(($settings = $uploader->checkServerSettings()) === true) {
                $result = $uploader->handleUpload(Zend_Registry::get('profileImagesPath'), $this->_user->id);                
                if($this->_ajaxRequest) {
                    $this->_response->appendBody(Zend_Json::encode($result));
                    return;
                }
                else {
                    $this->view->uploadresponse = $result;
                    return $this->render('uploadstoryphotos');
                }
            }
            else {
                if($this->_ajaxRequest) {
                    $this->_response->appendBody(Zend_Json::encode($settings));
                    return;
                }
                else {
                    $this->view->uploadresponse = $settings;
                    return $this->render('uploadstoryphotos');
                }
            }
        }
        else 
            return $this->_redirect('/profile'); 
    }

    public function cropprofilepicAction() {
        if($this->getRequest()->isPost() && $this->_ajaxRequest) {
            $values = $this->getRequest()->getPost();
            $width = 200;
            $height = 250;
            $quality = 100;
            $src = Zend_Registry::get('profileImagesPath').$values['filename'];
            list($originalWidth, $originalHeight) = getimagesize($src);
            //Get Proportions From Interface Maxed Out at 450px
            /*if($originalWidth > 450) {
                $scale = 450/$originalWidth;
                $values['width'] *= (1/$scale);
                $values['height'] *= (1/$scale);
            }*/
            $img_r = imagecreatefromjpeg($src);
            $dest_r = imagecreatetruecolor($width, $height);
            imagecopyresampled($dest_r, $img_r, 0, 0, $values['x1'], $values['y1'], $width, $height, $values['width'], $values['height']);
            imagejpeg($dest_r, $src, $quality);
            if(file_exists($src)) {
                $thumbGenerator = new File_Adapter_Image_Filter(Zend_Registry::get('profileThumbsPath'));
                $thumbGenerator->setConfig(200, 200, 80, 'jpeg');
                $thumbGenerator->generateThumb(Zend_Registry::get('profileImagesPath').$values['filename']);
                if(file_exists(Zend_Registry::get('profileThumbsPath').$values['filename'])) {
                    $profileService = new Service_Profile();
                    if($profileService->editProfile($this->_user->profileid, array('photo' => $values['filename']))) {
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
    }
    
    public function addstoryAction() {
        if($this->getRequest()->isPost() & $this->_ajaxRequest) {
            $story = $this->getRequest()->getParams();
            if(isset($story['content'])) {
                $storyService = new Service_Userstory();
                if(is_array($newStory = $storyService->addNew($this->_user->id, $story))) {
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
            return $this->_redirect('/profile');
    }
    
    public function addstorycommentAction() {
        if($this->getRequest()->isPost() && $this->_ajaxRequest) {
            if(($comment = $this->getRequest()->getParam('comment', FALSE)) && ($story = $this->getRequest()->getParam('story', FALSE))) {
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
    
    public function deleteownedstorycommentAction() {
        if($this->getRequest()->isPost() && $this->_ajaxRequest) {
            if($commentid = $this->getRequest()->getParam('comment', FALSE)) {
                $storyService = new Service_Userstory();
                if($storyService->deleteOwnedStoryComment($commentid)) {
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
    
    
    public function requestfriendshipAction() {
        if($this->getRequest()->isPost() && $this->_ajaxRequest) {
            if($user = $this->getRequest()->getParam('user', FALSE)) {
                $friendService = new Service_Friend();
                if(is_array($result = $friendService->addRequest($this->_user->id, $user))) {
                    $this->_response->appendBody("1");
                    $messageService = new Service_Message();
                    $userService = new Service_User();
                    $userProfile = $userService->getUserProfile($this->_user->id);
                    $subject = "New Friend Request";
                    $content = "Hi<br/><br/>You have received a friend request from ".$userProfile->fName." ".$userProfile->lName.".<br/><br/>Please <a target='_blank' href='user/acceptrequest/".$result['id']."'>Accept</a> or <a target='_blank' href='user/rejectrequest/".$result['id']."'>Reject</a>";
                    $messageService->addNew($this->_user->id, $user, 'n', NULL, $subject, $content);
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
            return $this->_redirect('/profile');
        }
    }

    public function acceptrequestAction() {
        if($this->getRequest()->isGet() && !$this->_ajaxRequest) {
            if($request = $this->getRequest()->getParam('requestid')) {
                $friendService = new Service_Friend();
                if(is_array($result = $friendService->acceptRequest($request, $this->_user->id))) {
                    $userService = new Service_User();
                    $userProfile = $userService->getUserProfile($result['friend']);
                    $redirect = '/'.$userProfile->displayName.'/view';
                    return $this->_redirect($redirect);
                }
                else {
                    $this->view->error = "Sorry. There was a problem processing your request. Please try again later.";
                    return $this->render('friendrequesterror');
                }
            }
            else {
                $this->view->error = "Sorry. There was a problem processing your request. Please try again later.";
                return $this->render('friendrequesterror');
            }
        }
        else if($this->_ajaxRequest) {
            if($request = $this->getRequest()->getParam('requestid', FALSE))  {
                $friendService = new Service_Friend();
                if(is_array($result = $friendService->acceptRequest($request, $this->_user->id))) {
                    $this->_response->appendBody("1");
                    return;
                }
                else {
                    $this->_response->appendBody("0");
                    return;
                }
            }  
            else {
                $this->_response->appendBody("0");
                return;
            }
        }
    }

    public function rejectrequestAction() {
        if($this->getRequest()->isGet() && !$this->_ajaxRequest) {
            if($request = $this->getRequest()->getParam('requestid', FALSE)) {
                $friendService = new Service_Friend();
                if($result = $friendService->deleteRequest($request, $this->_user->id)) {
                    return $this->_render('requestrejectedsuccess');
                }
                else {
                    $this->view->error = "Sorry. There was a problem processing your request. Please try again later.";
                    return $this->render('friendrequesterror');
                }
            }
            else {
                $this->view->error = "Sorry. There was a problem processing your request. Please try again later.";
                return $this->render('friendrequesterror');
            }
        }
        else if($this->_ajaxRequest) {
            if($request = $this->getRequest()->getParam('requestid', FALSE))  {
                $friendService = new Service_Friend();
                if($result = $friendService->deleteRequest($request, $this->_user->id)) {
                    $this->_response->appendBody("1");
                    return;
                }
                else {
                    $this->_response->appendBody("0");
                    return;
                }
            }  
            else {
                $this->_response->appendBody("0");
                return;
            }
        }
    }
    
    public function searchAction() {
        if($this->getRequest()->isGet()) {
            if($terms = $this->getRequest()->getParam('searchTerms', FALSE)) {
                $userService = new Service_User();
                if(is_array($results = $userService->searchUsers($terms))) {
                    $this->_helper->layout->setLayout('topmenu');
                    for($i=0; $i < count($results); $i++) {
                        if($this->_user->profileid == $results[$i]['id']) {
                            unset($results[$i]);
                            continue;
                        }
                        $friended = false;
                        if(count($results[$i]['Friends'])) {
                            foreach($results[$i]['Friends'] as $friend) {
                                if($this->_user->id == $friend['friend']) {
                                    $friended = true;
                                    break;
                                }
                            }
                        }
                        $results[$i]['friend'] = $friended;
                        $results[$i]['connections'] = count($results[$i]['Friends']);
                        if(!$friended) {
                            $request = false;
                            if(count($results[$i]['OutgoingFriendRequests'])) {
                                foreach($results[$i]['OutgoingFriendRequests'] as $outgoing) {
                                    if($this->_user->id == $outgoing['requestee']) {
                                        $request = true;
                                        $results[$i]['requestid'] = $outgoing['id'];
                                        break;
                                    }
                                }
                            }
                            $results[$i]['incomingRequest'] = $request;
                            if(!$request) {
                                $requested = false;
                                if(count($results[$i]['IncomingFriendRequests'])) {
                                    foreach($results[$i]['IncomingFriendRequests'] as $incoming) {
                                        if($this->_user->id == $incoming['requestor']) {
                                            $requested = true;
                                            $results[$i]['requestid'] = $incoming['id'];
                                            break;
                                        }
                                    }
                                }
                                $results[$i]['outgoingRequest'] = $requested;
                            }
                        }
                    }
                    $result['root'] = $results;
                    $this->view->searchTerms = $terms;
                    $this->view->resultTotal = count($results);
                    $this->view->searchNoPerPage = 5;
                    $this->view->users = $result;
                    return $this->render('searchresults');
                }
            }
        }
    }
    
     public function getsettingsAction() {
        if($this->getRequest()->isGet() && $this->_ajaxRequest) {
            $profileService = new Service_Profile();
            if(is_array($profile = $profileService->fetchProfile($this->_user->id))) {
                $result['root'] = $profile;
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
     
    public function accountsettingsAction() {
        $this->_helper->layout->setLayout('single');
        $this->view->usergroup = $this->_user->usergroup;
    }
}
