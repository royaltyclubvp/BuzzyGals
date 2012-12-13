<?php

/**
 * 
 * Profile Service Class
 * 
 * @package             ElleFab
 * @subpackage          Model Service Layer
 * @author              Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * @version             0.1
 * 
 */
class Service_Profile extends Service_Base_Foundation {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Returns User Profile
     * 
     * @param   integer     $userid     User ID
     * @return  array | bool
     */
    public function fetchProfile($userid) {
        $query = Doctrine_Query::create()
                ->from('Model_Profile p')
                ->innerJoin('p.Location l')
                ->where('user = ?', $userid)
                ->limit(1);
        try {
            $results = $query->fetchOne(array(), Doctrine::HYDRATE_ARRAY);
        }
        catch (Doctrine_Exception $e) {
            return FALSE;
        }
        return $results;
    }
    
    /**
     * Return User Profile By Username
     * 
     * @param   string      $username   Username
     * @return  array | bool
     */
    public function fetchProfileByUsername($username) {
        $query = Doctrine_Query::create()
                ->from('Model_Profile p')
                ->innerJoin('p.Location l')
                ->where('p.displayName = ?', $username)
                ->limit(1);
        try {
            $results = $query->fetchOne(array(), Doctrine::HYDRATE_ARRAY);
        }
        catch (Doctrine_Exception $e) {
            return FALSE;
        }
        return $results;
    }
    
    /**
     * Edit User Profile Fields
     * 
     * @param   integer     $userid     User ID
     * @param   array       $changes    Profile Changes
     * @return  bool
     */
    public function editProfile($profileid, $changes) {
        $profile = new Model_Profile();
        $profile->assignIdentifier($profileid);
        $profile->fromArray($changes);
        try {
            $profile->save();
        }
        catch (Doctrine_Exception $e) {
            return FALSE;
        }
        return true;
    }
    
    
}
