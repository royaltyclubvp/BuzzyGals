<?php

class TestController extends Base_FoundationController {
    
    protected $service = null;
    
    public function init() {
        parent::init();
        $this->userService = new Service_User();
        $this->profileService = new Service_Profile();
        $this->friendService = new Service_Friend();
        $this->storyService = new Service_Userstory();
        $this->topicService = new Service_Topic();
        $this->resourceService = new Service_Resource();
        $this->messageService = new Service_Message();
        $this->articleService = new Service_Article();
        $this->locationService = new Service_Location();
    }
    
    public function testuserAction() {
        $user = array(
            'username' => 'test6@email.com',
            'password' => 'testing3',
            'usergroup' => 1,
            'verificationcode' => md5(uniqid(rand(), TRUE)).time(),
            'fName' => 'Kimora',
            'lName' => 'Simmons',
            'displayName' => 'tuser',
            'gender' => 'female',
            'interests' => 'Test Interests 4',
            'dob' => '1983-3-12',
            'email' => 'test3@email.com',
            'about' => 'About Test 3',
            'location' => 1
        );
        $this->view->user = $this->userService->addNew($user);
    }
    
    public function changepasswordAction() {
        $this->view->user = $this->userService->changeUserPassword(13, 'js32003');
        $this->render('testuser');
    }
    public function articleuriAction() {
        $this->view->result = $this->articleService->fetchOneByUri('new2');
    }
    
    public function getuserAction() {
        $this->view->user = $this->userService->getUser('id', 6)->toArray();
        $this->render('testuser');
    }
    
    public function verifytestAction() {
        $this->view->user = $this->userService->verifyAccount(6);
        $this->render('testuser');
    }
    
    public function getprofileAction() {
        $this->view->profile = $this->userService->getUserProfile(6);
    }
    
    public function searchresourceAction() {
        $this->view->result = $this->resourceService->searchResources('domestic');
        $this->render('friend');
    }
    public function editprofileAction() {
        $profile = array(
            'fName' => 'Edited Test',
            'lName' => 'Account-Name',
            'gender' => 'female'
        );
        if(($edited = $this->profileService->editProfile(3, $profile)) === TRUE) {
            $this->view->profile = $this->profileService->fetchProfile(6);
        }   
        else {
            $this->view->profile = $edited;
        }
        $this->render('getprofile');
    }

    public function uploadphotoAction() {
        if($this->getRequest()->isPost()) {
            if($_FILES['photo']['name'][0] != '') {
                $adapter = new Zend_File_Transfer_Adapter_Http();
                $adapter->setDestination(Zend_Registry::get('userImagesPath'));
                $files = $adapter->getFileInfo();
                $i = 1;
                foreach($files as $file => $info) {
                    if(!$adapter->isUploaded($file)) {
                        $this->view->sendConfirm = 'Problem uploading files';
                        return $this->render('error');
                    }
                    $extension = strtolower(end(explode('.', $info['name'])));
                    $name = time().'4'.$i.".".$extension;
                    $i++;
                    $adapter->addFilter('Rename', array('target' => Zend_Registry::get('userImagesPath').$name, 'overwrite' => TRUE));
                    if(!$adapter->receive($info['name'])) return $this->render('error');
                }
                $filename = $adapter->getFileName();
                $filename = basename($filename);
                $profile = array('photo'=>$filename);
                if(($edited = $this->profileService->editProfile(2, $profile)) === TRUE) {
                    $this->view->profile = $this->profileService->fetchProfile(2);
                }
                else {
                    $this->view->profile = $edited;
                }
                $this->render('getprofile');
            }
        }
    }

    public function addfriendAction() {
        $this->view->result = $this->friendService->addRequest(4, 6);
        $this->render('friend');
    }

    public function acceptrequestAction() {
        $this->view->result = $this->friendService->acceptRequest(4, 6);
        $this->render('friend');
    }
    
    public function deleterequestAction() {
        $this->view->result = $this->friendService->deleteRequest(1, 4);
        $this->render('friend');
    }
    
    public function fetchsentrequestsAction() {
        $this->view->result = $this->friendService->fetchSentRequests(4);
        $this->render('friend');
    }
    
    public function fetchreceivedrequestsAction() {
        $this->view->result = $this->friendService->fetchReceivedRequests(7);
        $this->render('friend');
    }
    
    public function getfriendsAction() {
        $this->view->result = $this->friendService->fetchFriends(4);
        $this->render('friend');
    }

    public function blockfriendAction() {
        $this->view->result = $this->friendService->blockFriend(3, 4);
        $this->render('friend');
    }
    
    public function unblockfriendAction() {
        $this->view->result = $this->friendService->unblockFriend(3, 4);
        $this->render('friend');
    }
    
    public function listresourcesAction() {
        if($topicName = $this->getRequest()->getParam('topic', FALSE)) {
            $topicService = new Service_Topic();
            if(is_array($topics = $topicService->fetchUrlList())) {
                if($topic = array_search($topicName, $topics)) {
                    //$this->_helper->layout->setLayout('topmenu');
                    $resourceService = new Service_Resource();
                    if(is_array($resources = $resourceService->fetchByTopic($topic))) {
                        for($i=0; $i < $resources['total']; $i++) {
                            $bookmarked = false;
                            if(count($resources['resources'][$i]['Bookmarkers'])) {
                                foreach($resources['resources'][$i]['Bookmarkers'] as $bookmarker) {
                                    if($this->_user->id = $bookmarker['id']) {
                                        $bookmarked = true;
                                        break;
                                    }
                                }
                            }
                            $resources['resources'][$i]['bookmarked'] = $bookmarked;
                            $resources['resources'][$i]['userid'] = $this->_user->id;
                        }
                        //$this->view->title = ucfirst($topicName);
                        $this->view->result = $resources['resources'];
                       //$this->view->resourceCount = $resources['total'];
                        return $this->render('friend');
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
    }
    
    public function searchusersAction() {
        if($this->getRequest()->isGet()) {
            if($terms = $this->getRequest()->getParam('searchTerms', FALSE)) {
                $userService = new Service_User();
                if(is_array($results = $userService->searchUsers($terms))) {
                    //$this->_helper->layout->setLayout('topmenu');
                    for($i=0; $i < count($results); $i++) {
                        $friended = false;
                        if(count($results[$i]['Friends'])) {
                            foreach($results[$i]['Friends'] as $friend) {
                                if(12 == $friend['friend']) {
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
                                    if(12 == $outgoing['requestee']) {
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
                                        if(12 == $incoming['requestor']) {
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
                    $this->view->result = $result;
                    return $this->render('friend');
                }
            }
        }
    }
    
    public function addstoryAction() {
        $story = array(
            'content' => 'This is the test user story',
            'gallery' => array(
                    '134431946941.jpg','13479636641.jpg','134431952441.jpg'
                )
        );
        
        $this->view->result = $this->storyService->addNew(4, $story);
    }
    
    public function getstoriesAction() {
        $this->view->result = $this->storyService->fetch(4, 1);
        $this->render('addstory');
    }
    
    public function addcommentAction() {
        $comment = "This is a test comment. Atomic addition test";
        $this->view->result = $this->storyService->addNewComment(4, 4, $comment);
        $this->render('addstory');
    }
    
    public function deletestoryAction() {
        $this->view->result = $this->storyService->deleteStory(2, 4);
        $this->render('addstory');
    }
    
    public function deletecommentAction() {
        $this->view->result = $this->storyService->deleteStoryComment(1, 4);
        $this->render('addstory');
    }
    
    public function followstoryAction() {
        $this->view->result = $this->storyService->followStory(4, 4);
        $this->render('addstory');
    }

    public function unfollowstoryAction() {
        $this->view->result = $this->storyService->unfollowStory(4, 4);
        $this->render('addstory');
    }
    
    public function getusercommentsAction() {
        $this->view->result = $this->storyService->fetchUserComments(4);
        $this->render('addstory');
    }
    
    public function getstorycommentsAction() {
        $this->view->result = $this->storyService->fetchComments(1);
        $this->render('addstory');
    }
    
    public function addtopicAction() {
        $topic = array(
            'name' => 'Finance',
            'description' => 'This is the finance townhall'
        );
        $this->view->result = $this->topicService->addNew($topic);
        $this->render('topic');
    }
    
    public function edittopicAction() {
        $this->view->result = $this->topicService->edit(1, array('description' => 'This is the modified finance townhall'));
        $this->render('topic');
    }
    
    public function deletetopicAction() {
        $this->view->result = $this->topicService->delete(2);
        $this->render('topic');
    }
    
    public function changeavailabilityAction() {
        $this->view->result = $this->topicService->makeAvailable(1);
        $this->render('topic');
    }
    
    public function gettopicsAction() {
        $this->view->result = $this->topicService->fetchAll();
        $this->render('topic');
    }
    
    public function fetchtownhallsAction() {
        $this->view->result = $this->topicService->fetchFollowed(4);
        $this->render('topic');
    }
    
    public function addresourceAction() {
        $resource = array(
            'name' => 'Domestic Violence and Rape Crisis Services of Saratoga County',
            'notes' => 'DVRC help families become economically sufficient and strong enough to leave their abusive homes, other services include shelters and awarness programs.',
            'type' => 3,
            'state' => 1,
            'city' => 1,
            'country' => 1,
            'contact' => '<p>Office: 518-583-0280<br/>Domestic Violence Hotline: 518-584-8188<br/>Rape Crisis Hotline: 518-587-2336<br/>Website: <a href="http://www.dvrcsaratoga.org/">http://www.dvrcsaratoga.org/</a>',
            'address' => '480 Broadway Saratoga Springs, NY 12866',
            'national' => 0
        );    
        $this->view->result = $this->resourceService->addNew($resource);
        $this->render('resource');
    }
    
    public function editresourceAction() {
        $resource = array(
            'name' => 'Women Center for Disability',
            'national' => 1
        );    
        $this->view->result = $this->resourceService->editResource(1, $resource);
        $this->render('resource');
    }
    
    public function bookmarkresourceAction() {
        $this->view->result = $this->resourceService->addBookmark(2, $this->_user->id);
        $this->render('resource');
    }
    
    public function fetchbookmarkedAction() {
        $this->view->result = $this->articleService->fetchBookmarked(4);
        $this->render('resource');
    }
    
    public function removebookmarkAction() {
        $this->view->result = $this->resourceService->removeBookmark(2, 4);
        $this->render('resource');
    }
    
    public function getresourcesAction() {
        $this->view->result = $this->resourceService->fetchBookmarked(4);
        $this->render('resource');
    }
    
    public function getresourcebytopicAction() {
        $this->view->result = $this->resourceService->fetchByTopic(1,4);
        $this->render('resource');
    }
    
    public function getresourcebycityAction() {
        $this->view->result = $this->resourceService->fetchByCity(1);
        $this->render('resource');
    }
    
    public function newmsgAction() {
        $recipients = array(4);
        $this->view->result = $this->messageService->addNew(7, $recipients,'n',NULL,'Test Message','This is the test message');
        $this->render('message');
    }
    
    public function getmsgAction() {
        $this->view->result = $this->messageService->fetchMessage(6, 4);
        $this->render('message');
    }
   
    public function deletemsgAction() {
        $this->view->result = $this->messageService->deleteMessage(11, 4);
        $this->render('message');
    }
    
    public function readmsgAction() {
        $this->view->result = $this->messageService->read(11, 7);
        $this->render('message');
    }
    
    public function getmessagesAction() {
        $this->view->result = $this->messageService->fetchReceived(4);
        $this->render('message');
    }
    
    public function getsentmessagesAction() {
        $this->view->result = $this->messageService->fetchSent(4);
        $this->render('message');
    }
    
    public function addauthorAction() {
        $this->view->result = $this->articleService->addNewAuthor('Dr. Seuss');
        $this->render('article');
    }

    public function changeauthorAction() {
        $this->view->result = $this->articleService->editAuthor(2,'Changed Author Name Attempt 2');
        $this->render('article');
    }
    
    public function deleteauthorAction() {
        $this->view->result = $this->articleService->deleteAuthor(3);
        $this->render('article');
    }
    
    public function addarticleAction() {
        $article = array(
            'author' => 1,
            'avatar_image' => '134779384341.jpg',
            'title_image' => '134779636641.jpg',
            'topic' => 2,
            'title' => 'Test Article Number 2',
            'date' => date('Y-m-d'),
            'available' => 1,
            'content' => 'This is the article\'s test content',
            'description' => 'This is a description of the article. It should only be a limited length, in order to meet the restrictions.',
            'uri' => 'new2'
        );
        $this->view->result = $this->articleService->addNew($article);
        $this->render('article');
    }

    public function editarticleAction() {
        $changes = array(
            'title' => 'Test Article No. 1'
        );
        $this->view->result = $this->articleService->editArticle(1, $changes);
        $this->render('article');
    }
    
    public function followarticleAction() {
        $this->view->result = $this->articleService->followArticle(4, 2);
        $this->render('article');
    }
    
    public function unfollowarticleAction() {
        $this->view->result = $this->articleService->unfollowArticle(4, 2);
        $this->render('article');
    }
    
    public function getarticlesAction() {
        $this->view->result = $this->articleService->fetchByAuthor(1);
        $this->render('article');
    }
    
    public function addarticlecommentAction() {
        $this->view->result = $this->articleService->addComment(4, 2, 'This is the test comment');
        $this->render('article');
    }
    
    public function deletearticlecommentAction() {
        $this->view->result = $this->articleService->deleteComment(4, 1);
        $this->render('article');
    }
    
    public function getarticleAction() {
        $this->view->result = $this->articleService->fetchOneByUri('new2');
        $this->render('article');
    }
    
    public function getnewestarticlesAction() {
        $this->view->result = $this->articleService->fetchNewest(1,2,2);
        $this->render('article');
    }
        
    public function genindexAction() {
        $this->view->result = Doctrine_Core::generateSqlFromArray(array('Model_Resource'));
        $this->render('friend');
    }
    
    public function gentablesAction() {
        Doctrine_Core::createTablesFromArray(array('Model_Resource'));
        $this->render('friend');
    }
    
    public function messagecountAction() {
        $this->view->result = $this->messageService->messageCount(7);
        $this->render('message');
    }

    public function bytesAction() {
        $upload = new File_Adapter_Uploader("image", array('jpg', 'png'), 50000);
        $this->view->result = $upload->_toBytes("128M");
        $this->render('message');
    }
    
    public function fetchnewestAction() {
        $this->view->result = $this->resourceService->fetchNewest();
        $this->render('message');
    }
    
    public function fetchstateAction() {
        $this->view->result = $this->resourceService->fetchByState(1);
        $this->render('message');
    }

    public function getlocationsAction() {
        $this->view->result = $this->locationService->getLocationList('country');
        $this->render('message');
    }
    
    public function getlocationAction() {
        $this->view->result = $this->locationService->locationExists(2, 1, 1);
        $this->render('message');
    }
    
    public function getnotifsAction() {
        $this->view->result = $this->storyService->fetchByUsersAndTime(1, array(4));
        $this->render('message');
    }
    
    public function getfriendlistAction() {
        $this->view->result = $this->friendService->fetchFriendIds(4);
        $this->render('message');
    }
    
    public function checkauthorAction() {
        $this->view->result = $this->articleService->checkAuthor('Dr. Seuss');
        $this->render('message');
    }

    public function flagarticleAction() {
        $this->view->result = $this->articleService->flag(13, 1, "test");
        $this->render('message');
    }
    
    public function flagcountAction() {
        $this->view->result = $this->articleService->flagCount(1);
        $this->render('message');
    }
    
    public function removeflagsAction() {
        $this->view->result = $this->articleService->removeFlags(1);
        $this->render('message');
    }
}
