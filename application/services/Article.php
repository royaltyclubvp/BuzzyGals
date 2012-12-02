<?php

/**
 * Article Service Class
 * 
 * @package         ElleFab
 * @subpackage      Model Service Layer
 * @author          Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * @version         0.1
 */
class Service_Article extends Service_Base_Foundation {
    
    protected   $cache;
    
    public function __construct() {
        parent::__construct();
        $this->cache = new Service_Cache_Article();
    }
    
    /**
     * Adds New Article
     * 
     * @param   array       $article    Article
     * @return  array | bool     
     */
    public function addNew($article) {
        $new = new Model_Featuredarticles();
        $new->fromArray($article);
        $new->created = date('Y-m-d H:i:s');
        try {
            $new->save();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $new->toArray();
    }
    
    /**
     * Add New Featured Author
     * 
     * @param   string      $author     Author Name
     * @return  array | bool
     */
    public function addNewAuthor($author) {
        $new = new Model_FeaturedAuthor();
        $new->fromArray(array(
            'name' => $author,
            'created' => date('Y-m-d')
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
     * Edit Author Name
     * 
     * @param   integer     $author     Author ID
     * @param   string      $change     Change
     * @return  bool
     */
    public function editAuthor($author, $change) {
        $query = Doctrine_Query::create()
                ->update('Model_FeaturedAuthor')
                ->set('name','?',$change)
                ->set('modified','?',date('Y-m-d H:i:s'))
                ->where('id = ?', $author);
        try {
            $results = $query->execute();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results;
    }
    
    /**
     * Delete Author Record
     * 
     * @param   integer     $author     Author ID
     * @return  bool
     */
    public function deleteAuthor($author) {
        $query = Doctrine_Query::create()
                ->delete('Model_FeaturedAuthor')
                ->where('id = ?', $author);
        try {
            $results = $query->execute();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results;
    }
    
    /**
     * Edit Article 
     * 
     * @param   integer     $article    Article ID
     * @param   array       $changes    Changes
     * @return  bool | array
     */
    public function editArticle($article, $changes) {
        $new = new Model_Featuredarticles();
        $new->assignIdentifier($article);
        $new->fromArray($changes);
        try {
            $new->save();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $new->toArray();
    }
    
    /**
     * Follow Article
     * 
     * @param   integer     $user       User ID
     * @param   integer     $article    Article ID
     * @param   bool | array
     */
    public function followArticle($user, $article) {
        $new = new Model_Followedarticle();
        $new->fromArray(array(
            'user' => $user,
            'article' => $article,
            'date' => date('Y-m-d')
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
     * Unfollowed Article
     * 
     * @param   integer     $user       User ID
     * @param   integer     $article    Article ID
     * @param   bool
     */
    public function unfollowArticle($user, $article) {
        $query = Doctrine_Query::create()->delete('Model_Followedarticle')
                ->where('user = ?', $user)
                ->andWhere('article = ?', $article);
        try {
            $results = $query->execute();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results;
    }
    
    /**
     * Retrieves Articles By Topic
     * 
     * @param   integer       $topic      Topic ID
     * @return  array | bool
     */
    public function fetchByTopic($topic, $total = FALSE) {
        $query = Doctrine_Query::create()->from('Model_Featuredarticles fa')
                ->innerJoin('fa.Topic t')
                ->where('t.id = ?', $topic);
        try {
            if($total) $results = $query->count();
            else {
                $results['total'] = $query->count();
                $results['articles'] = $query->fetchArray();                  
            }
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results;
    }
    
    
    /**
     * Retrieves Articles By Author
     * 
     * @param   integer       $author     Author ID
     * @return  array | bool
     */
    public function fetchByAuthor($author) {
        $query = Doctrine_Query::create()->from('Model_Featuredarticles fa')
                ->innerJoin('fa.Author a')
                ->where('a.id = ?', $author);
        try {
            $results = $query->execute();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results->toArray();
    }
    
    /**
     * Add Article Comment
     * 
     * @param   integer     $user       User ID
     * @param   integer     $article    Article ID
     * @param   string      $comment    Comment
     * @return  array | bool
     */
    public function addComment($user, $article, $comment) {
        $new = new Model_Articlecomment();
        $new->fromArray(array(
            'date' => date('Y-m-d H:i:s'),
            'article' => $article,
            'content' => $comment,
            'user' => $user
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
     * Delete an Article Comment
     * 
     * @param   integer     $user       User ID
     * @param   integer     $comment    Comment ID
     * @return  bool
     */
    public function deleteComment($user, $comment) {
        $query = Doctrine_Query::create()->delete('Model_Articlecomment c')
                ->where('c.id = ?', $comment)
                ->andWhere('c.user = ?', $user);
        try {
            $result = $query->execute();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }
    
    /**
     * Retrieves a Single Article
     * 
     * @param   integer     $article    Article ID
     * @return  array       
     */
    public function fetchOne($article) {
        $query = Doctrine_Query::create()
                ->from('Model_Featuredarticles fa')
                ->innerJoin('fa.Author a')
                ->leftJoin('fa.Comments c')
                ->innerJoin('c.UserProfile p')
                ->where('fa.id = ?', $article);
        try {
            $results = $query->fetchArray();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results[0];
    }
    
    /**
     * Fetches One Article Via URI
     * 
     * @param   string      $uri        URI String
     * @param   bool        $view       Page View Indicator
     * @return  array | bool
     */
    public function fetchOneByUri($uri) {
        $query = Doctrine_Query::create()
                ->from('Model_Featuredarticles fa')
                ->innerJoin('fa.Author a')
                ->innerJoin('fa.Topic t')
                ->leftJoin('fa.Comments c')
                ->leftJoin('c.UserProfile p')
                ->leftJoin('fa.Followers f')
                ->where('fa.uri = ?', $uri);
        try {
            $results = $query->fetchArray();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        $this->addView($results[0]['topic'], $results[0]['id']);
        return $results[0];
    }
    
    /**
     * Record Article View
     * 
     * @param   integer     $topicid        Topic ID
     * @param   integer     $articleid      Article ID
     * 
     */
    public function addView($topicid, $articleid) {
        $this->cache->addArticleView($articleid, $topicid);
        return;
    }
    
    /**
     * Retrieves Newest Articles
     * 
     * @param   integer     $topic      Topic ID
     * @param   integer     $page       Page Number Desired | Default = 1
     * @param   integer     $noPerPage  Results Per Page | Default = 5
     * @return  array | bool
     */
    public function fetchNewest($topic, $page = 1, $noPerPage = 5) {
        $pager = new Doctrine_Pager(
            Doctrine_Query::create()->from('Model_Featuredarticles fa')
                            ->innerJoin('fa.Topic t')
                            ->innerJoin('fa.Author a')
                            ->where('t.id = ?', $topic)
                            ->orderby('fa.created DESC'),
                $page,
                $noPerPage
        );
        try {
            $results['articles'] = $pager->execute();
            $results['total'] = $pager->getNumResults();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        $results['articles'] = $results['articles']->toArray();
        return $results;
    }
    
    /**
     * Retrieves Popular Articles
     * 
     * @param   integer     $topic      Topic ID
     * @return  array | bool
     */
    public function fetchPopular($topic) {
        if(is_array($articles = $this->cache->fetchList($topic))) {
            if($articles['last_database_write'] + 3600 > time())
                return $articles['articles'];
            else {
                $views = $this->cache->refreshPopularArticles($topic);
                $connection = Doctrine_Manager::connection();
                $articleTable = Doctrine_Core::getTable('Model_Featuredarticles');
                $count = 0;
                foreach($views as $articleid => $newViews) {
                    $article[$count] = $articleTable->find($articleid);
                    $article[$count++]->views += $newViews;
                }
                $connection->flush();
                $query = Doctrine_Query::create()
                        ->from('Model_Featuredarticles fa')
                        ->innerJoin('fa.Topic t')
                        ->innerJoin('fa.Author a')
                        ->where('t.id = ?', $topic)
                        ->orderby('fa.views DESC')
                        ->limit(5);
                try {
                    $results = $query->fetchArray();
                }
                catch(Doctrine_Exception $e) {
                    return $e->getMessage();
                }
                $this->cache->writeList($results, $topic);
                return $results;
            }   
        }
        $query = Doctrine_Query::create()
                        ->from('Model_Featuredarticles fa')
                        ->innerJoin('fa.Topic t')
                        ->innerJoin('fa.Author a')
                        ->where('t.id = ?', $topic)
                        ->orderby('fa.views DESC')
                        ->limit(5);
        try {
          $results = $query->fetchArray();
        }
        catch(Doctrine_Exception $e) {
            return $e->getMessage();
        }
        if($this->cache->writeList($results, $topic))
        return $results;   
    }
     
    
    /**
     * Retrieves Bookmarked Articles
     * 
     * @param   integer     $user       User ID
     * @param   integer     $page       Page Number Desired | Default = 1
     * @param   integer     $noPerPage  Results Per Page | Default = 10
     * @return  array | bool
     */
    public function fetchBookmarked($user, $page = 1, $noPerPage = 10) {
        $pager = new Doctrine_Pager(
            Doctrine_Query::create()->from('Model_Featuredarticles a')
                            ->select('a.date, a.title, a.uri, t.name')
                            ->innerJoin('a.Followers f')
                            ->innerJoin('a.Topic t')
                            ->where('f.id = ?', $user)
                            ->orderby('a.date DESC'),
            $page,
            $noPerPage
        );
        try {
            $results = $pager->execute();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $results->toArray();
    }
}
