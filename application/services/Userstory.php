<?php

/**
 * 
 * Userstory Service Class
 * 
 * @package         ElleFab
 * @subpackage      Model Service Layer
 * @author          Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * @version         0.1
 * 
 */
class Service_Userstory extends Service_Base_Foundation {
    
    public function __construct() {
       parent::__construct();
    }
    
    /**
     * Adds new User Story
     * 
     * @param   integer     $user       User ID
     * @param   array       $story      User Story 
     * @return  array | bool     
     */
    public function addNew($user, $story) {
        $new = new Model_Userstory();
        $new->fromArray(array(
            'user' => $user,
            'content' => $story['content'],
            'date' => date('Y-m-d H:i:s')
        ));
        if(isset($story['gallery']) && is_array($story['gallery'])) {
            $storyMedia = new Model_Userstorymedia();
            $storyMedia->photos = serialize($story['gallery']);
            $storyMedia->totalphotos = count($story['gallery']);
        }
        if(isset($story['videos']) && is_array($story['videos'])) {
            if(!isset($storyMedia)) $storyMedia = new Model_Userstorymedia();
            $storyMedia->videos = serialize($story['videos']);
            $storyMedia->totalvideos = count($story['videos']);
        }    
        if(isset($storyMedia)) $new->Media = $storyMedia;
        try {
            $new->save();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $new->toArray();
    }
    
    /**
     * Adds New Story Comment
     * 
     * @param   integer     $user       User ID
     * @param   integer     $story      Story ID
     * @param   string      $comment    Comment Text
     * @return  array | bool
     */ 
    public function addNewComment($user, $story, $comment) {
        $new = new Model_Userstorycomment();
        $new->fromArray(array(
            'user' => $user,
            'story' => $story,
            'content' => $comment,
            'date' => date('Y-m-d H:i:s')
        ));
        try {
            $new->save();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        $new['User']['Profile'];
        return $new->toArray();
    }
    
    /**
     * Deletes New Story
     * 
     * @param   integer     $story      Story ID
     * @param   integer     $user       User ID
     * @return  bool | array
     */
    public function deleteStory($story, $user) {
        $query = Doctrine_Query::create()->delete('Model_Userstory')
                ->where('id = ?',$story)
                ->andWhere('user = ?', $user);
        try {
            $results = $query->execute();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results;
    }
    
    /**
     * Deletes Story Comment
     * 
     * @param   integer     $comment     Comment ID
     * @param   integer     $user        User ID
     * @return  bool | array
     */
    public function deleteStoryComment($comment, $user) {
        $query = Doctrine_Query::create()->delete('Model_Userstorycomment')
                ->where('id = ?',$comment)
                ->andWhere('user = ?', $user);
        try {
            $results = $query->execute();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results;
    }
    
    /**
     * Deletes Owned Story Comment
     * 
     * @param   integer     $comment     Comment ID
     * @param   integer     $user        User ID
     * @return  bool | array
     */
    public function deleteOwnedStoryComment($comment) {
        $query = Doctrine_Query::create()->delete('Model_Userstorycomment')
                ->where('id = ?',$comment);
        try {
            $results = $query->execute();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results;
    }
    
    /**
     * Follow User Story
     * 
     * @param   integer     $story      Story ID
     * @param   integer     $user       User ID
     * @return  bool 
     */
    public function followStory($story, $user) {
        $new = new Model_Followedstory();
        $new->fromArray(array(
            'user' => $user,
            'story' => $story,
            'date' => date('Y-m-d H:i:s')
        ));
        try {
            $new->save();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return true;
    }
    
    /**
     * Unfollow User Story
     * 
     * @param   integer     $story      Story ID
     * @param   integer     $user       User ID
     * @return  bool 
     */
    public function unfollowStory($story, $user) {
        $query = Doctrine_Query::create()->delete('Model_Followedstory')
                ->where('story = ?', $story)
                ->andWhere('user = ?', $user);
        try {
            $results = $query->execute();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results;
    }
    
    /**
     * Retrieves User Comments
     * 
     * @param   integer     $user       User ID
     * @return  array | bool
     */
    public function fetchUserComments($user) {
        $query = Doctrine_Query::create()
                ->from('Model_Userstorycomment c')
                ->innerJoin('c.Story s')
                ->where('c.user = ?', $user);
        try {
            $results = $query->fetchArray();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results;
    }
   
    /**
     * Retrieve User Stories 
     * 
     * @param   integer     $user       User ID
     * @param   integer     $page       Page Number Desired | Default = 1
     * @param   integer     $noPerPage  Results Per Page | No of Results Per Page = 5
     * @return  array | bool
     */
    public function fetch($user, $page = 1, $noPerPage = 5) {
        $pager = new Doctrine_Pager(
            Doctrine_Query::create()->from('Model_Userstory s')
                            ->leftJoin('s.Media m')
                            ->leftJoin('s.Comments c')
                            ->leftJoin('c.User u')
                            ->leftJoin('u.Profile p')
                            ->where('s.user = ?', $user)
                            ->orderby('s.date DESC'),
            $page,
            $noPerPage
        );
        try {
            $results = $pager->execute();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        for($i = 0; $i < count($results); $i++) {
            if(isset($results[$i]->Media)) {
                $results[$i]->Media->photos = unserialize($results[$i]->Media->photos);
                $results[$i]->Media->videos = unserialize($results[$i]->Media->videos);
            }
        }
        return $results->toArray();
    }
    
    /**
     * Retrieve User Story Comments
     * 
     * @param   integer     $story      Story ID
     * @return  array | bool
     */
    public function fetchComments($story) {
        $query = Doctrine_Query::create()
                ->from('Model_Userstorycomment')
                ->where('story = ?', $story);
        try {
            $results = $query->fetchArray();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results;
    }
}
