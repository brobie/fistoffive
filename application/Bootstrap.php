<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initSession(){
		$userOptions = $this->getApplication()->getOptions();
		$idleTimeout = $userOptions['resources']['session']['idle_timeout'];
		
		$sessionOptions = array(
			'gc_probability'    =>    $userOptions['resources']['session']['gc_probability'],
			'gc_divisor'        =>    $userOptions['resources']['session']['gc_divisor'],
			'gc_maxlifetime'    =>    $userOptions['resources']['session']['gc_maxlifetime'],
			'save_path'    		=>    $userOptions['resources']['session']['save_path'],
			'cookie_domain'     =>    $userOptions['resources']['session']['cookie_domain']
		);
		
		Zend_Session::start($sessionOptions);
		
		
	}
	
	protected function _initAutoLoader(){
		$this->getApplication()->setAutoloaderNamespaces(array('Fof_'));

		$autoLoader = Zend_Loader_Autoloader::getInstance();
		$autoLoader->setFallbackAutoloader(true);
 
		$autoloader = new Zend_Application_Module_Autoloader(
			array(
        		'namespace' => 'Fof_',
            	'basePath'  => APPLICATION_PATH . '/modules/default',
        		'resourceTypes' => 
        			array(
        				'forms'=>array('path'=>'/forms', 'namespace'=>'Form'),
						'models'=>array('path'=>'/models', 'namespace'=>'Model'),
        				'plugins'=>array('path'=>'/plugins', 'namespace'=>'Plugin'),
        				'acl'=>array('path'=>'/acls', 'namespace'=>'Acl')
					)
			)
		);
		
	}

	
	protected function _initView()
	{
		// Initialize view
		$view = new Zend_View();

		$view->headPhtml = 'head.phtml';
		$view->headerPhtml = 'header.phtml';
		$view->navigationPhtml = 'navigation.phtml';
		$view->messagesPhtml = 'messages.phtml';
		$view->footerPhtml = 'footer.phtml';
		
		$view->bodyId = 'single';
		$view->bodyClass = '';
		$view->containerClass = 'container_16';
		$view->nofollow = false;
		
		$view->doctype('XHTML1_STRICT');

		$view->env = APPLICATION_ENV;
		$view->addHelperPath(APPLICATION_PATH . '/modules/default/views/helpers', 'Fof_Helper_');
		
		// Add it to the ViewRenderer
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'ViewRenderer'
            );
            $viewRenderer->setView($view);

            // Return it, so that it can be stored by the bootstrap
            return $view;
	}

	
	/**
	* Initializes the cache
	*
	* @return nothing
	*/
	protected function _initCache(){
		$frontend= array('lifetime' => 86400, 'automatic_serialization' => true);
	
		// We use a helper class to get us our ini values.  The value in the ini file is: BASE_PATH "/tmp/cache/"
		$backend= array('cache_dir' => Fof_Model_Util::getProperty('cache_directory'), 'hashed_directory_level' => 2);
	
		$cache = Zend_Cache::factory('core', 'File', $frontend, $backend);
	
		Zend_Registry::set('cache',$cache);
		
		Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
	
	}

	protected function _initModules()
	{
		$this->bootstrap('frontController');
		$frontController = Zend_Controller_Front::getInstance();

		$frontController->setParam('useDefaultControllerAlways', true);
		
		$frontController->setRequest(new Zend_Controller_Request_Http());

	}
	

}
