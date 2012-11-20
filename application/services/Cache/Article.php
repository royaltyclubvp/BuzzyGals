<?php

/**
 * Article Service Cache Class
 * 
 * @package         BuzzyGals
 * @subpackage      Model Service Layer
 * @author          Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * @version         0.1
 */

class Service_Cache_Article {
	
	protected $cacheDirectory = "";
    protected $frontend = "";
    protected $backend = "";
    protected $frontendOptions = array();
    protected $backendOptions = array();
    protected $cache;
    protected $listIndexName = 'list';
    protected $articleCountIndex = 'articleViews';
	
	public function __construct() {
	    //Set Directory
		$this->cacheDirectory = Zend_Registry::get('articlesCachePath');
        //Set Names 
        $this->frontend = "Core";
        $this->backend = "File";
        //Set Options
        $this->frontendOptions = array_merge(array(
            'caching'=> true,
            'cache_id_prefix' => 'poparticles',
            'lifetime' => NULL,
            'automatic_serialization' => true,
        ),$this->frontendOptions);
        $this->backendOptions = array_merge(array(
            'cache_dir' => $this->cacheDirectory,
            'file_name_prefix'=> 'poparticles'
        ), $this->backendOptions);
        $this->cache = Zend_Cache::factory($this->frontend, $this->backend, $this->frontendOptions, $this->backendOptions);
	}
    
    public function fetchList($topic) {
        if(!($this->cache->test($this->listIndexName.$topic))) 
            return false;
        else 
            return $this->cache->load($this->listIndexName.$topic);
    }
    
    public function addArticleView($articleid, $topic) {
        if(!($this->cache->test($this->articleCountIndex.$topic))) {
            $articleViewCount = array();
            $articleViewCount[$articleid] = 1;
            $this->cache->save($articleViewCount, $this->articleCountIndex.$topic);
        }
        else {
            $articleViewCount = $this->cache->load($this->articleCountIndex.$topic);
            if(isset($articleViewCount[$articleid]))
                $articleViewCount[$articleid] += 1;
            else {
                $articleViewCount[$articleid] = 1;
            }
            $this->cache->save($articleViewCount, $this->articleCountIndex.$topic);
        }
    }
    
    public function refreshPopularArticles($topic) {
        if($this->cache->test($this->listIndexName.$topic)) {
            $this->cache->remove($this->listIndexName.$topic);
        }
        $views = array();
        if($this->cache->test($this->articleCountIndex.$topic)) {
            $views = $this->cache->load($this->articleCountIndex.$topic);
            $this->cache->remove($this->articleCountIndex.$topic);
        }
        return $views;
    }
    
    public function writeList($newArticles, $topic) {
        $new = array();
        $new['last_database_write'] = time();
        $new['articles'] = $newArticles;
        if($this->cache->test($this->listIndexName.$topic)) 
            return false;
        else {
            $this->cache->save($new, $this->listIndexName.$topic);
            return true;
        }
    }
}
