<?php

/**
 * Resource Service Class
 * 
 * @package         ElleFab
 * @subpackage      Model Service Layer
 * @author          Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * @version         0.1
 */
class Service_Resource extends Service_Base_Foundation {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Add New Resource
     * 
     * @param   array       $resource       Resource Details
     * @return  bool | array
     */
    public function addNew($resource) {
        $new = new Model_Resource();
        $new->fromArray($resource);
        try {
            $new->save();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $new->toArray();
    }
    
    /**
     * Bookmark Resource
     * 
     * @param   integer     $resource       Resource ID
     * @param   integer     $user           User ID
     * @return  bool | array
     */
    public function addBookmark($resource, $user) {
        $new = new Model_Bookmarkedresource();
        $new->fromArray(array(
            'user' => $user,
            'resource' => $resource,
            'date' => date('Y-m-d H:i:s')
        ));
        try {
            $new->save();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $new->toArray();
    }
    
    /**
     * Remove Bookmark
     * 
     * @param   integer     $resource       Resource ID
     * @param   integer     $user           User ID
     * @return  bool
     */
    public function removeBookmark($resource, $user) {
        $query = Doctrine_Query::create()
                ->delete('Model_BookmarkedResource')
                ->where('resource = ?', $resource)
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
     * Edit Resource 
     * 
     * @param   integer     $resource    Article ID
     * @param   array       $changes    Changes
     * @return  bool | array
     */
    public function editResource($resource, $changes) {
        $new = new Model_Resource();
        $new->assignIdentifier($resource);
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
     * Delete Resource 
     * 
     * @param   integer     $resource       Resource ID
     * @return  bool
     */
    public function deleteResource($resource) {
        $query = Doctrine_Query::create()->delete('Model_Resource')
                ->where('id = ?', $resource);
        try {
            $results = $query->execute();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results;
    }
    
    /**
     * Retrieve Resources By Type
     * 
     * @param   integer     $type       	Type ID
     * @return  bool | array
     */
    public function fetchByType($type) {
	    $query = Doctrine_Query::create()
	    		->from('Model_Resource r')
	    		->innerJoin('r.Type t')
	    		->where('t.id = ?', $type);
	    try {
	    	$results = $query->fetchArray();
	    }
	    catch (Doctrine_Exception $e) {
	    	return $e->getMessage();
	    }
	    return $results;
    }
    
    /**
     * Retrieve Resources By Topic Association
     * 
     * @param   integer     $topic          Topic ID
     * @param   integer     $user           Requesting User ID
     * @return  bool | array
     */
    public function fetchByTopic($topic, $user, $total = FALSE) {
        $query = Doctrine_Query::create()
                ->from('Model_Resource r')
                ->innerJoin('r.Resourcetopics t')
                ->leftJoin('r.Bookmarkers b')
                ->where('t.topic = ?', $topic);
        try {
            if($total) $results = $query->count();
            else {
                $results['total'] = $query->count();
                $results['resources'] = $query->fetchArray();                  
            }
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results;
    }
    
    /**
     * Retrieve Bookmarked Resources 
     * 
     * @param   integer     $user           User ID
     * @return  bool | array
     */
    public function fetchBookmarked($user) {
        $query = Doctrine_Query::create()
                ->from('Model_Resourcetypes t')
                ->innerJoin('t.Resources r')
                ->innerJoin('r.Bookmarkers b')
                ->where('b.id = ?', $user);
        try {
            $results = $query->fetchArray();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results;
    }
    
    /**
     * Retrieve Resources By City
     * 
     * @param   integer     $city      City ID
     * @param   integer     $page      Page Number Desired | Default = 1
     * @param   integer     $noPerPage Results Per Page | Default = 5
     * @return  bool | array
     */
    public function fetchByCity($city, $page = 1, $noPerPage = 5) {
        $pager = new Doctrine_Pager(
            Doctrine_Query::create()->from('Model_Resource r')
                            ->leftJoin('r.Bookmarkers b')
                            ->where('r.city = ?', $city)
                            ->orderby('r.created DESC'),
                     $page,
                     $noPerPage
        );
        try {
            $results['resources'] = $pager->execute();
            $results['total'] = $pager->getNumResults();
        }        
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        $results['resources'] = $results['resources']->toArray();
        return $results;
    }
    
    /**
     * Retrieve Newest Resources 
     * 
     * @param   integer     $limit      Retrieval Limit
     * @return  bool | array
     */
    public function fetchNewest($limit = 5) {
        $query = Doctrine_Query::create()
                ->from('Model_Resource r')
                ->leftJoin('r.Bookmarkers b')
                ->orderby('r.created DESC')
                ->limit($limit);
        try {
            $results = $query->fetchArray();
        }
        catch(Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results;
    }
    
}
