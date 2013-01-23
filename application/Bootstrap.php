<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function _initDoctrine() {
        require_once 'Doctrine.php';
        $autoLoader = Zend_Loader_Autoloader::getInstance();
        $autoLoader->pushAutoloader(array('Doctrine', 'autoload'));
        $doctrineConfig = $this->getOption('doctrine');
        $doctrineManager = Doctrine_Manager::getInstance();

        //Establish Doctrine Settings
        $doctrineManager->setAttribute(Doctrine::ATTR_MODEL_LOADING, Doctrine::MODEL_LOADING_CONSERVATIVE);
        $doctrineManager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, TRUE);
        $doctrineManager->setAttribute(Doctrine_Core::ATTR_VALIDATE, Doctrine_Core::VALIDATE_ALL);
        $doctrineManager->setAttribute(Doctrine_Core::ATTR_USE_DQL_CALLBACKS, TRUE);
        $doctrineManager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, TRUE);
        $doctrineManager->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, TRUE);
        $doctrineManager->setAttribute(Doctrine_Core::ATTR_QUERY_LIMIT, Doctrine_Core::LIMIT_RECORDS);

        //Establish Connection
        $doctrineManager->openConnection($doctrineConfig['connection_string']);
        $doctrineManager->connection()->setCharset('utf8');

        return $doctrineManager;
    }

	protected function _initDoctype() {
		$this->bootstrap('view');
		$view = $this->getResource('view');
		$view->doctype('HTML5');
	}
    
    protected function _initAutoload() {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
            'basePath' => APPLICATION_PATH,
            'namespace' => '',
            'resourceTypes' => array(
                'service' => array(
                    'path' => 'services/',
                    'namespace' => 'Service_'
                ),
                'basecontroller' => array(
                    'path' => 'controllers/Base/',
                    'namespace' => 'Base_'
                ),
				'authadapter' => array(
				    'path' => 'adapters/Auth/',
                	'namespace' => 'Auth_Adapter_'
				),
				'model' => array(
                    'path' => 'models/',
                    'namespace'=> 'Model_'
                ),
                'fileadapter' => array(
                    'path' => 'adapters/file',
                    'namespace' => 'File_Adapter_'
                )
            )
        ));
		$libraryResourceLoader = new Zend_Loader_Autoloader_Resource(array(
	            'basePath' => ZEND_LIBRARY_PATH,
	            'namespace' => '',
	            'resourceTypes' => array(
	                'crypto' => array(
	                    'path' => 'Cryptography/',
	                    'namespace' => 'Cryptography_'
	                ),
	                'image' => array(
                        'path' => 'Polycast/',
                        'namespace' => 'Polycast_'
                    ),
                    'zf2' => array(
                        'path' => 'Zend2/',
                        'namespace' => 'Zend2_'
                    )
	            )
	        ));
        return $autoloader;
    }

    protected function _initZf2() {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->pushAutoloader(new Zend2_Loader_Autoloader_Zf2(), 'Zend');
    }

    protected function _initSpecialRoutes() {
        $router = Zend_Controller_Front::getInstance()->getRouter();
        //Register Link
        $router->addRoute(
            'signup', new Zend_Controller_Router_Route_Static('signup', array(
                'controller' => 'user',
                'action' => 'signup'
            ))
        );
        //User Search Link
        $router->addRoute(
            'usersearch', new Zend_Controller_Router_Route_Static('user/search', array(
                'controller' => 'profile',
                'action' => 'search'
            ))
        );
        //Verification Link
        $router->addRoute(
            'verify', new Zend_Controller_Router_Route('account/verify/:verificationcode/:userid', array(
                'controller' => 'user',
                'action' => 'verify'
            ))
        );
        //Logout Link
        $router->addRoute(
            'logout', new Zend_Controller_Router_Route_Static('logout', array(
                'controller' => 'user',
                'action' => 'logout'
            ))
        );
        //Login Link
        $router->addRoute(
            'login', new Zend_Controller_Router_Route_Static('login', array(
                'controller' => 'user',
                'action' => 'login'
            ))
        );
        //View Friend Profile Link
        $router->addRoute(
            'friendProfile', new Zend_Controller_Router_Route(':username/view', array(
                'controller' => 'profile',
                'action' => 'friendprofile'
            ))
        );
        //Townhall
        $router->addRoute(
            'townhall', new Zend_Controller_Router_Route('townhall/:name', array(
                'controller' => 'townhalls',
                'action' => 'index'
            ))
        );
        //Topic Resources
        $router->addRoute(
            'resourcetopics', new Zend_Controller_Router_Route(':topic/resources', array(
                'controller' => 'resources',
                'action' => 'index'
            ))
        );
        //Override For Resource Bookmark
        $router->addRoute(
            'bookmarkResource', new Zend_Controller_Router_Route_Static('resources/bookmark', array(
                'controller' => 'resources',
                'action' => 'bookmark'
            ))
        );
        //Override For Search Resources
        $router->addRoute(
            'searchResourcs', new Zend_Controller_Router_Route_Static('resources/search', array(
                'controller' => 'resources',
                'action' => 'search'
            ))
        );
        //Townhall Articles
        $router->addRoute(
            'article', new Zend_Controller_Router_Route('featured/:uri', array(
                'controller' => 'townhalls',
                'action' => 'article'
            ))
        );
        
    }

	protected function _initSession() {
		Zend_Session::start();
	}
    
    protected function _initSecuritysettings() {
        $settings = $this->getOption('security');
        Zend_Registry::set('maxAccessAttempts', $settings['maxAccessAttempts']);   
    }

    protected function _initUserpaths() {
        $Config = $this->getOption('storage');
        Zend_Registry::set('basepath', $Config['basePath']);
        Zend_Registry::set('userImagesPath', $Config['basePath'].$Config['userImagesPath']);
        Zend_Registry::set('userGalleryImagesPath', $Config['basePath'].$Config['userGalleryImagesPath']);
        Zend_Registry::set('userGalleryThumbsPath', $Config['basePath'].$Config['userGalleryThumbsPath']);
        Zend_Registry::set('profileImagesPath', Zend_Registry::get('userImagesPath').$Config['profileImagesDir']);
        Zend_Registry::set('profileThumbsPath', Zend_Registry::get('userImagesPath').$Config['profileThumbsDir']);
        Zend_Registry::set('userImagesUrl', $Config['userImagesUrl']);
        Zend_Registry::set('profileImagesUrl', $Config['profileImagesUrl']);
        Zend_Registry::set('profileThumbsUrl', $Config['profileThumbsUrl']);
        Zend_Registry::set('articleImagesUrl', $Config['articleImagesUrl']);
        Zend_Registry::set('userGalleryImagesUrl', $Config['userGalleryImagesUrl']);
        Zend_Registry::set('userGalleryThumbsUrl', $Config['userGalleryThumbsUrl']);
    }
	
	protected function _initCachepaths() {
		$Config = $this->getOption('cache');
        $PathConfig = $this->getOption('storage');
        Zend_Registry::set('cacheBasePath', $PathConfig['basePath']);
		Zend_Registry::set('articlesCachePath', Zend_Registry::get('cacheBasePath').$Config['articlesCachePath']);
        Zend_Registry::set('topicsCachePath', Zend_Registry::get('cacheBasePath').$Config['topicsCachePath']);
	}
    
    protected function _initEmailSettings() {
        $Config = $this->getOption('email');
        Zend_Registry::set('registrationEmail', $Config['registrationEmail']);
        Zend_Registry::set('registrationSender', $Config['registrationSender']);
    }
    
    protected function _initSitePaths() {
        $Config = $this->getOption('site');
        Zend_Registry::set('baseUrl', $Config['baseUrl']);
    }
}

