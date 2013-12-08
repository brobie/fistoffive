<?php
define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));
define('APPLICATION_PATH', BASE_PATH . '/application');

set_include_path(
    BASE_PATH . '/library' . PATH_SEPARATOR . APPLICATION_PATH 
    . PATH_SEPARATOR . get_include_path()
);

if (getenv('HTTP_APPLICATION_ENV')){
	$app_env_key = 'HTTP_APPLICATION_ENV';
} else {
	$app_env_key = 'APPLICATION_ENV';
}
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv($app_env_key) ? getenv($app_env_key) : 'development'));

require_once "Zend/Loader/Autoloader.php";
                                        

require_once 'Zend/Application.php';

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'   
);


$bootstrap = $application->getBootstrap();
$bootstrap->bootstrap('db');
$dbAdapter = $bootstrap->getResource('db');


Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);


$registry = Zend_Registry::getInstance();
$registry->application = $application;
$registry->dbAdapter     = $dbAdapter;


unset($dbAdapter, $registry);

$application->bootstrap();

$application->run();