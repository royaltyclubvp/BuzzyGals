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
				    'path' => 'adapters/auth/',
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
	                )
	            )
	        ));
        return $autoloader;
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
        Zend_Registry::set('profileImagesPath', Zend_Registry::get('userImagesPath').$Config['profileImagesDir']);
        Zend_Registry::set('userImagesUrl', $Config['userImagesUrl']);
        Zend_Registry::set('profileImagesUrl', $Config['profileImagesUrl']);
        Zend_Registry::set('articleImagesUrl', $Config['articleImagesUrl']);
        Zend_Registry::set('userGalleryImagesUrl', $Config['userGalleryImagesUrl']);
    }
	
	protected function _initCachepaths() {
		$Config = $this->getOption('cache');
		Zend_Registry::set('articlesCachePath', $Config['articlesCachePath']);
        Zend_Registry::set('topicsCachePath', $Config['topicsCachePath']);
	}
}

