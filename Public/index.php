<?php
/**
 * Nova - PHP 5 Framework
 *
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2012 Thomas Frei
 * @link        https://github.com/thomasfrei/nova
 * @license     https://github.com/thomasfrei/nova/blob/master/License.txt
 * @package     Nova
 * @version     0.0.1
 */

/**
 * Setting the Environment and Error Reporting
 * options are: development|testing|production
 */
$env = 'development';

defined("ENVIRONMENT") or define("ENVIRONMENT", $env);

switch(ENVIRONMENT)
{
	case 'production' :
		error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
		ini_set('display_errors', 0);
		break;
	case 'testing':
	case 'development':
		error_reporting(-1);
		ini_set('display_errors', 1);
		break;
		
}

/**
 * PHP 5.3 will complain if you don't set a timezone.
 */
date_default_timezone_set('Europe/Zurich');	

/**
 * Defining some Constants
 */
defined("BASEPATH") or define("BASEPATH", realpath(dirname(__FILE__).'/../') .'/');
defined("APPPATH") or define("APPPATH", BASEPATH. 'Application/');
defined("SYSPATH") or define("SYSPATH", BASEPATH. 'Library/');

/**
 * Set the Syspath as the include path
 */
set_include_path(SYSPATH);

/**
 * Instantiate the Autoloader
 */
require_once SYSPATH. '/Nova/Loader/Autoloader.php';
$autoloader = new Nova\Loader\Autoloader();
$autoloader->registerAutoloader();


// xdebug_start_trace("/var/www/Nova/Logs/trace");

/**
 * Instantiate the Front Controller
 */
$front = Nova\Controller\Front::getInstance();
$front->setModuleDirectory(APPPATH);
$front->dispatch();

// xdebug_stop_trace();
