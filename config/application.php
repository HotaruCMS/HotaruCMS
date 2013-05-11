<?php

// Derived Constants
define('APP_PATH', dirname(dirname(__FILE__)) . '/');
define('WWW_BASE_PATH', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));
define('WWW_CSS_PATH', WWW_BASE_PATH . 'css/');
define('WWW_JS_PATH', WWW_BASE_PATH . 'js/');
define('WWW_IMAGE_PATH', WWW_BASE_PATH . 'images/');
// set_include_path(get_include_path() . PATH_SEPARATOR . APP_PATH . PATH_SEPARATOR . SHARED_PATH);
// set_include_path(get_include_path() . PATH_SEPARATOR . APP_PATH);

// Include and configure the LightVC framework (http://lightvc.org/)
include_once(APP_PATH . 'libs/modules/lightvc/lightvc.php');
Lvc_Config::addControllerPath(APP_PATH . 'content/controllers/');
Lvc_Config::addControllerViewPath(APP_PATH . 'content/views/');
Lvc_Config::addLayoutViewPath(APP_PATH . 'content/views/layouts/');
Lvc_Config::addElementViewPath(APP_PATH . 'content/views/elements/');
Lvc_Config::setViewClassName('AppView');
include(APP_PATH . 'libs/classes/AppController.class.php');
include(APP_PATH . 'libs/classes/AppView.class.php');
include(APP_PATH . 'config/routes.php');

/* Enable the optional Autoloader and/or SimpleReflector helpers by uncommenting the following:
// Setup Autoloader (http://anthonybush.com/projects/autoloader/)
include(APP_PATH . 'classes/Autoloader.class.php');
Autoloader::setCacheFilePath(APP_PATH . 'tmp/class_path_cache.txt');
Autoloader::excludeFolderNamesMatchingRegex('/^CVS|\..*$/');
Autoloader::setClassPaths(array(
	APP_PATH . 'classes/',
	// APP_PATH . 'models/'
));
spl_autoload_register(array('Autoloader', 'loadClass'));

// Setup SimpleReflector alias (http://anthonybush.com/projects/simplereflector/)
// call this to debug a variable/object, e.g. jam($var);
function jam() { call_user_func_array(array('SimpleReflector', 'jam'), func_get_args()); }
//*/

?>