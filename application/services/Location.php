<?php

/**
 * 
 * Location Service Class
 * 
 * @package         BuzzyGals
 * @subpackage      Model Service Layer
 * @author          Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * @version         0.1
 * 
 */
class Service_Location extends Service_Base_Foundation {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Check for Existence of Location Entry
     * 
     * @param   integer     $city       City ID
     * @param   integer     $state      State ID
     * @param   integer     $country    Country ID
     * @return  bool | integer
     */
    public function locationExists($city, $state, $country) {
        $query = Doctrine_Query::create()
                ->from('Model_Location')
                ->where('cityid = ?', $city)
                ->andWhere('stateprovid = ?', $state)
                ->andWhere('countryid = ?', $country);
        try {
            $results = $query->fetchArray();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        if(count($results))
            return $results[0]['id'];
        else
            return false;
    }
    
    /**
     * Retrieve Location List | By Either Country, State or City
     * 
     * @param   string      $type       Request Type - 'city','state','country'
     * @param   integer     $id         Identification Variable | Default = NULL
     */
    public function getLocationList($type, $id = NULL) {
        $query = Doctrine_Query::create();
        if($type=='country') {
            $query->from('Model_LocationCountry country')
                ->leftJoin('country.States states')
                ->leftJoin('states.Cities cities');
        }
        else if($type=='state') {
            $query->from('Model_LocationState state')
                ->leftJoin('state.Cities cities');
            if($id != NULL) $query->where('state.country = ?', $id);
        }
        else if($type=='city') {
            $query->from('Model_LocationCity city');
            if($id != NULL) $query->where('city.state = ?', $id);
        }
        try {
            $results = $query->fetchArray();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results;
    }
    
    /**
     * Add New Location 
     * 
     * @param   array      $locationParts      Array of Location Parts
     * @return  bool | int
     */
    public function addLocation($locationParts) {
        $newLocation = new Model_Location();
        $newLocation->fromArray($locationParts);
        try {
            $newLocation->save();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $newLocation->id;
    }
}
