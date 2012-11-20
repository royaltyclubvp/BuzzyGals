<?php

/**
 * Topic Service Cache Class
 * 
 * @package         BuzzyGals
 * @subpackage      Model Service Layer
 * @author          Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * @version         0.1
 */

class Service_Cache_Topic {
    
    protected $cacheDirectory = "";
    protected $frontend = "";
    protected $backend = "";
    protected $frontendOptions = array();
    protected $backendOptions = array();
    protected $cache;
    protected $listIndexName = 'list';
    protected $urlListIndexName = 'urllist';
    
    public function __construct() {
        //Set Directory
        $this->cacheDirectory = Zend_Registry::get('topicsCachePath');
        //Set Names
        $this->frontend = "Core";
        $this->backend = "File";
        //Set Options
        $this->frontendOptions = array_merge(array(
            'caching' => true,
            'cache_id_prefix' => 'topics',
            'lifetime' => NULL,
            'automatic_serialization' => TRUE
        ), $this->frontendOptions);
        $this->backendOptions = array_merge(array(
            'cache_dir' => $this->cacheDirectory,
            'file_name_prefix' => 'topics'
        ), $this->backendOptions);
        $this->cache = Zend_Cache::factory($this->frontend, $this->backend, $this->frontendOptions, $this->backendOptions);
    }  
    
    public function fetchList($urlArray = false) {      
        if(!($this->cache->test( ($urlArray) ? $this->urlListIndexName : $this->listIndexName ) )) 
            return false;
        else
            return $this->cache->load( ($urlArray) ? $this->urlListIndexName : $this->listIndexName );
    }
    
    public function refreshList($list, $urlArray = false) {
        if($this->cache->test( ($urlArray) ? $this->urlListIndexName : $this->listIndexName )) {
            $this->cache->remove( ($urlArray) ? $this->urlListIndexName : $this->listIndexName );
            $this->cache->save($list,  ($urlArray) ? $this->urlListIndexName : $this->listIndexName );
            return true;
        }
        else
            return false;
    }
    
    public function writeList($list, $urlArray = false) {
        if($this->cache->test( ($urlArray) ? $this->urlListIndexName : $this->listIndexName))
            return false;
        else {
            $this->cache->save($list, ($urlArray) ? $this->urlListIndexName : $this->listIndexName );
            return true;
        }            
    }
}
