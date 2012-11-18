<?php

/**
 * Topic Service Class
 * 
 * @package         ElleFab
 * @subpackage      Model Service Layer
 * @author          Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * @version         0.1
 */
class Service_Topic extends Service_Base_Foundation {
    
    protected $cache;
    
    public function __construct() {
        parent::__construct();
        $this->cache = new Service_Cache_Topic();
    }
    
    /**
     * Add New Topic
     * @param   array       $topic         Resource Details
     * @return  bool | array
     */
    public function addNew($topic) {
        $new = new Model_Topic();
        $new->fromArray($topic);
        try {
            $new->save();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $new->toArray();
    }
    
    /**
     * Edit Topic
     * 
     * @param   integer     $topic      Topic ID
     * @param   array       $changes    Changes
     * @return  bool
     */
    public function edit($topic, $changes) {
        $new = new Model_Topic();
        $new->assignIdentifier($topic);
        $new->fromArray($changes);
        try {
            $new->save();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return true;
    }
    
    /**
     * Delete Topic
     * 
     * @param   integer     $topic      Topic ID
     * @return  bool
     */
    public function delete($topic) {
        $query = Doctrine_Query::create()->delete('Model_Topic')
                ->where('id = ?', $topic);
        try {
            $results = $query->execute();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results;
    }
    
    /**
     * Make Topic Unavailable
     * 
     * @param   integer     $topic      Topic ID
     * @return  bool
     */
    public function makeUnavailable($topic) {
        $query = Doctrine_Query::create()->update('Model_Topic')
                ->set('active', 0)
                ->where('id = ?', $topic);
        try {
            $results = $query->execute();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results;
    }
    
    /**
     * Make Topic Available
     * 
     * @param   integer     $topic      Topic ID
     * @return  bool
     */
    public function makeAvailable($topic) {
        $query = Doctrine_Query::create()->update('Model_Topic')
                ->set('active', 1)
                ->where('id = ?', $topic);
        try {
            $results = $query->execute();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results;
    }
    
    /**
     * Retrieve All Topics
     * 
     * @return  array | bool
     */
    public function fetchAll() {
        return Doctrine_Core::getTable('Model_Topic')->findAll(Doctrine_Core::HYDRATE_ARRAY);
    }
    
    public function fetchUrlList() {
        if(is_array($results = $this->cache->fetchList(TRUE))) {
            return $results;
        }
        $query = Doctrine_Query::create()
                ->from('Model_Topic t')
                ->where('t.active = ?',1);
        try {
            $topics = $query->fetchArray();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        $total = count($topics);
        $results = array();
        for($i = 0; $i < $total; $i++) {
            $results[$topics[$i]['id']] = strtolower(preg_replace('/\s+/', '', $topics[$i]['name']));
        }
        $this->cache->writeList($results, true);
        return $results;
    }
    
    /**
     * Retrieve User Topics
     * 
     * @param   integer     $user       User ID
     * @return  array | bool
     */
    public function fetchFollowed($user) {
        $query = Doctrine_Query::create()
                ->select('ft.id, t.name')
                ->from('Model_Followedtopic ft')
                ->innerJoin('ft.User u')
                ->innerJoin('ft.Topic t')
                ->where('u.id = ?', $user);
        try {
            $results = $query->fetchArray();
        }
        catch(Doctrine_Exception $e) {
            return false;
        }
        return $results;
    } 

    /**
     * Follow Topic
     * 
     * @param   integer     $user       User ID
     * @param   integer     $topic      Topic ID
     * @return  array | bool
     */
    public function followTopic($user, $topic) {
        $new = new Model_Followedtopic();
        $new->fromArray(array(
            'user' => $user,
            'topic' => $topic
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
     * Unfollow Topic
     * 
     * @param   integer     $user       User ID
     * @param   integer     $topic      Topic ID
     * @return  array | bool
     */
    public function unfollowTopic($user, $topic) {
        $query = Doctrine_Query::create()->delete('Model_Followedtopic')
                                ->where('user = ?', $user)
                                ->andWhere('topic = ?', $topic);
        try {
            $results = $query->execute();
        }
        catch (Doctrine_Exception $e) {
            return false;
        }
        return $results;
    }
}
