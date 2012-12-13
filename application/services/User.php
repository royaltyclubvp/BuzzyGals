<?php

/**
 * 
 * User Service Class
 * 
 * @package         ElleFab
 * @subpackage      Model Service Layer
 * @author          Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * @version         0.1
 * 
 */
class Service_User extends Service_Base_Foundation {
    
   public function __construct() {
       parent::__construct();
   }
   
   /**
    * Adds new user record
    * 
    * @param    array   $user   User Array
    * @return   array | bool
    */
   public function addNew($user) { //To modify for Locations. Add Location, then Add User with Reference.
       $new = new Model_User();
       $new->fromArray($user);
       $newprofile = new Model_Profile();
       $newprofile->fromArray($user);
       $new->Profile = $newprofile;
       try {
           $new->save();
       }
       catch (Doctrine_Exception $e) {
           return $e->getMessage();
       }
       return $new->toArray();
   }
   
   /**
    * Checks existence of User by specified field
    * 
    * @param    string  $field  Field Name
    * @param    string  $value  Value 
    * @return   bool        
    */
    public function exists($field, $value) {
        $user = Doctrine_Core::getTable('Model_User')->findOneBy($field, $value);
        if(!$user) return false;
        else return true;
    }
   
   /**
    * Returns User Object 
    * 
    * @param    string  $field  Field Name to Find By
    * @param    string  $value  Supplied Field Value
    * @return   User
    */
	public function getUser($field, $value) {
		if(strtolower($field) == 'id') {
		    return Doctrine_Core::getTable('Model_User')->find($value);
		}
        return Doctrine_Core::getTable('Model_User')->findOneBy($field, $value);
	}
    
    /**
     * Returns User Profile
     * 
     * @param   integer         $userid         User ID
     * @return  Profile
     */
     public function getUserProfile($userid) {
         $query = Doctrine_Query::create()
                    ->from('Model_Profile p')
                    ->leftJoin('p.Location l')
                    ->where('p.user = ?', $userid)
                    ->limit(1);
         try {
             $results = $query->execute();
         }
         catch(Doctrine_Exception $e) {
             return $e->getMessage();
         }
         return $results[0];
     }
	
	/**
	 * Change User Password
	 *
	 *	@param		integer		$userid			User ID
	 *	@param		string		$newpassword	New Password
	 *	@return 	bool
	 */
	public function changeUserPassword($userid, $newpassword) {
	    $user = new Model_User();
        $user->assignIdentifier($userid);
		$user->password = $newpassword;
        try {
            $user->save();
        }
        catch (Doctrine_Exception $e) {
            return FALSE;
        }
        return true;
	}
	
	/**
	 * Register Login
	 * 
	 * @param		integer		$userid			User ID
	 * @return		bool
	 */
	 public function registerLogin($userid) {
	    $timestamp = date('Y-m-d H:i:s');
	    $query = Doctrine_Query::create()->update('Model_User')
                ->set('lastLogin','?', $timestamp)
                ->set('accessFailures',0)
                ->where('id = ?', $userid);
        try {
            $results = $query->execute();
        } 
        catch (Doctrine_Exception $e) {
            return FALSE;
        }
        return $results;
	 }
	 
	 /**
	  * Register Access Failure
	  * 
	  * 
	  * @param		integer		$userid			User ID
	  * @return		integer                     Number Indicating new total consecutive failures 
	  * 
	  */
	  public function registerFailure($userid) {
	    $user = $this->getUser('id', $userid);
		$user->accessFailures += 1;
        try {
            $user->save();
        }
        catch (Doctrine_Exception $e) {
            return FALSE;
        }
        return $user->accessFailures;
	  }
      
      /**
       * Disable User Account
       * 
       * @param     integer     $userid         User ID
       * @return    bool
       */
       public function disableAccount($userid) {
           $query = Doctrine_Query::create()->update('Model_User')
                    ->set('enabled', 0)
                    ->where('id = ?', $userid);
           try {
               $results = $query->execute();
           }
           catch (Doctrine_Exception $e) {
               return FALSE;
           }
           return $results;
       }
       
       /**
        * Enable User Account
        * 
        * @param    integer     $userid         User ID
        * @return   bool        
        */
        public function enableAccount($userid) {
            $query = Doctrine_Query::create()->update('Model_User')
                    ->set('enabled', 1)
                    ->where('id = ?', $userid);
            try {
                $results = $query->execute();
            }
            catch (Doctrine_Exception $e) {
                return FALSE;
            }
            return $results;
        }
        
        /**
         * Change User Account status to Verified
         * 
         * @param   integer     $userid         User ID
         * @return  bool
         */
         public function verifyAccount($userid) {
             $query = Doctrine_Query::create()->update('Model_User')
                        ->set('verified', 1)
                        ->set('verificationcode', 'NULL')
                        ->where('id = ?', $userid);
             try {
                 $results = $query->execute();
             }
             catch (Doctrine_Exception $e) {
                 return FALSE;
             }
             return true;
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
        if(!count($ids)) return array();
        $query = Doctrine_Query::create()
                ->from('Model_Profile p')
                ->leftJoin('p.Friends f')
                ->leftJoin('p.Location l')
                ->leftJoin('p.IncomingFriendRequests ifr')
                ->leftJoin('p.OutgoingFriendRequests ofr')
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
