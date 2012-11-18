<?php

/**
 * Friend Service Class
 * 
 * @package         ElleFab
 * @subpackage      Model Friend Layer
 * @author          Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * @version         0.1
 */
class Service_Friend extends Service_Base_Foundation {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Adds New Friend Request
     * 
     * @param   integer     $requestor      Requesting User's ID
     * @param   integer     $requestee      Requested User's ID
     * @return  array | bool
     */
     public function addRequest($requestor, $requestee) {
         $new = new Model_Friendrequest();
         $new->fromArray(array(
            'requestor' => $requestor,
            'requestee' => $requestee
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
      * Accept Friend Request
      * 
      * @param  integer     $request        Request ID
      * @param  integer     $user           Accepting User's ID
      * @return bool
      */
     public function acceptRequest($request, $user) {
         $requestQuery = Doctrine_Query::create()
                        ->from('Model_Friendrequest fr')
                        ->where('fr.id = ?',$request)
                        ->andWhere('fr.requestee = ?',$user);
         try {
             $req = $requestQuery->fetchArray();
         }               
         catch (Doctrine_Exception $e) {
             return $e->getMessage();
         }
         if(!count($req)) return false;
         $friendid = $req[0]['requestor'];
         try {
             $manager = $this->doctrineManager;
             $connection = $manager::connection();
             $connection->beginTransaction();
             $new = new Model_Friendship();
             $created = date('Y-m-d H:i:s');
             $new->user = $user;
             $new->friend = $friendid;
             $new->status = 'a';
             $new->created = $created;
             $new->save();
             $new2 = new Model_Friendship();
             $new2->user = $friendid;
             $new2->friend = $user;
             $new2->status = 'a';
             $new2->created = $created;
             $new2->save();
             $query = Doctrine_Query::create()->delete('Model_Friendrequest')->where('id = ?',$request);
             $delete = $query->execute();
             $connection->commit();
         }
         catch (Doctrine_Exception $e) {
             $connection->rollback();
             return $e->getMessage();
         }
         if(!$delete) {
             $connection->rollback();
             return false;
         }
         return $new->toArray();
     }
    
    /**
     * Delete Friend Request
     * 
     * @param   integer     $request        Request ID
     * @param   integer     $user           ID of User Involved in Transaction
     * @return  bool         
     */     
     public function deleteRequest($request, $user) {
         $query = Doctrine_Query::create()->delete('Model_Friendrequest')
                ->where('id = ?', $request)
                ->andWhere('requestor = ?',$user)
                ->orWhere('requestee = ?', $user);
         try {
             $results = $query->execute();
         }
         catch (Doctrine_Exception $e) {
             return $e->getMessage();
         }
         return $results;
     }
     
     /**
      * Retrieve Received Requests
      * 
      * @param  integer     $user           User ID
      * @return array | bool
      */
     public function fetchReceivedRequests($user) {
         $query = Doctrine_Query::create()
                ->from('Model_Friendrequest fr')
                ->innerJoin('fr.Requestor r')
                ->innerJoin('r.Profile p')
                ->where('fr.requestee = ?',$user);
         try {
             $results = $query->fetchArray();
         }
         catch (Doctrine_Exception $e) {
             return $e->getMessage();
         }
         return $results;
     }
     
     /**
      * Retrieve Sent Requests
      * 
      * @param  integer     $user           User ID
      * @return array | bool 
      */
     public function fetchSentRequests($user) {
         $query = Doctrine_Query::create()
                ->from('Model_Friendrequest fr')
                ->innerJoin('fr.Requestee r')
                ->innerJoin('r.Profile p')
                ->where('fr.requestor = ?',$user);
         try {
             $results = $query->fetchArray();
         }
         catch (Doctrine_Exception $e) {
             return $e->getMessage();
         }
         return $results;
     }  
     
     /**
      * Deletes Specified Friendship
      * 
      * @param  integer     $friendship     Friendship ID
      * @param  integer     $user           User ID
      * @return bool
      */
     public function deleteFriendship($friendship, $user) {
         $query = Doctrine_Query::create()
                ->from("Model_Friendship")
                ->where('id = ?',$friendship)
                ->andWhere('user = ?',$user);
         try {
             $friendship = $query->fetchArray();
         }
         catch (Doctrine_Exception $e) {
             return $e->getMessage();
         }
         if(!count($friendship)) return false;
         try {
             $manager = $this->doctrineManager;
             $connection = $manager::connection();
             $connection->beginTransaction();
             $query = Doctrine_Query::create()->delete('Model_Friendship')->where('user = ?',$friendship->friend)->andWhere('friend = ?',$user);
             $inverseDelete = $query->execute();
             $friendship[0]->remove();
             $connection->commit();
         }
         catch (Doctrine_Exception $e) {
             $connection->rollback();
             return $e->getMessage();
         }
         if(!$inverseDelete) {
             $connection->rollback();
             return false;
         }
         return true;
     }

    /**
     * Retrieve User's Friend List
     * 
     * @param   integer     $user           User ID
     * @return  bool | array
     */
    public function fetchFriends($user) {
        $query = Doctrine_Query::create()
                ->from('Model_Friendship fs')
                ->select('fs.id, fs.status, f.id, p.id, p.displayName, p.photo, l.city, l.stateprov, l.country')
                ->innerJoin('fs.Friend f')
                ->innerJoin('f.Profile p')
                ->innerJoin('p.Location l')
                ->where('fs.user = ?', $user)
                ->andWhere('fs.status = ?','a');
        try {
            $friends = $query->fetchArray();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $friends;
    }
    
    /**
     * Changes User Friendship Status to Blocked
     * 
     * @param   integer     $friendship     Friendship ID
     * @param   integer     $user           User ID
     * @return  bool
     */
    public function blockFriend($friendship, $user) {
        $query = Doctrine_Query::create()
                ->update('Model_Friendship')
                ->set('status','?','b')
                ->where('id = ?', $friendship)
                ->andWhere('user = ?',$user);
        try {
            $result = $query->execute();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    /**
     * Changes User Friendship Status from Blocked
     * 
     * @param   integer     $friendship     Friendship ID
     * @param   integer     $user           User ID
     * @return  bool
     */
    public function unblockFriend($friendship, $user) {
        $query = Doctrine_Query::create()
                ->update('Model_Friendship')
                ->set('status','?','a')
                ->where('id = ?', $friendship)
                ->andWhere('user = ?',$user);
        try {
            $result = $query->execute();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }
    
    /**
     * Searches For Users
     * 
     * @param   array       $terms          Search Terms
     * @return  array | bool 
     */
    public function searchUsers($terms) {
        $profileTable = Doctrine_Core::getTable('Model_Profile');
        try {
            $results = $profileTable->search($terms);    
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        $ids = array();
        foreach($results as $result) {
            $ids[] = $result['id'];
        }
        $query = Doctrine_Query::create()
                ->from('Model_Profile')
                ->whereIn('id', $ids);
        try {
            $results = $query->fetchArray();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results;
    }
      
}
