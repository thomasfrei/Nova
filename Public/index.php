<?php
 
/**
 * The directory in which the application specific ressources are located
 * Without trailing slash
 */
$application = 'Application';

/**
 * The directory in which the system ressources are located
 * Without trailing slash
 */
$library = 'Library';

/**
 * Set the Environment
 * Valid options are 
 *
 * 	- development
 * 	- testing
 * 	- production
 *
 * This sets according error reporting levels in the bootstrapper
 */
$environment = 'development';

/**
 * This enables the function trace feature from xdebug and
 * should only be enabled in a development environment
 * @link http://xdebug.org/docs/execution_trace
 */
$tracing = true;

/**
 * Sets the directory in which the traces are stored.
 * Format: /path/to/logs/directory/filename without file extension
 */
$tracingDir = '/var/www/Nova/Logs/nova';

/**
 * This enables the internal profiler
 * Should only be enabled in a development environment
 * @todo Profiler Plugin doesn't exist yet
 */
$profiling = false;

/**
 * PHP will complain if you don't set a timezone.
 * @link http://www.php.net/manual/timezones
 */
date_default_timezone_set('Europe/Zurich');	

/**
 * This is the End of the standard Configuration
 */

// Define the full path to document root
defined('DOCROOT') or define('DOCROOT', realpath(dirname(__FILE__).'/../').DIRECTORY_SEPARATOR);

// Define absolute paths for configured directories
define("APPPATH", DOCROOT. $application.DIRECTORY_SEPARATOR);
define("SYSPATH", DOCROOT. $library.DIRECTORY_SEPARATOR);

// Define the application environment
define("ENVIRONMENT", $environment);

// Clean up 
unset($application, $library, $environment);

// Load the install check
if (file_exists('Install.php')) {
    return require_once('Install.php'); 
}

// Define the start time of the application, used for profiling
defined('NOVA_START_TIME') or define('NOVA_START_TIME', microtime(true));

// Define the memory usage at the start of the application, used for profiling
defined('NOVA_START_MEMORY') or	define('NOVA_START_MEMORY', memory_get_usage());

// Include an setup the autoloader
require_once(SYSPATH.'Nova/Loader/Autoloader.php');
$autoloader = new Nova\Loader\Autoloader();
$autoloader->register();

// Configure namespaces
$namespaces = array(
		'Application' => APPPATH,
	);
$autoloader->registerNamespaces($namespaces);
$autoloader->setIncludePath(array(APPPATH, SYSPATH));

// Set bootstrapper options
$options = array(
	'tracing' 			=> $tracing,
	'tracing.directory' => $tracingDir,
	'profiling'			=> $profiling,
);

// Start the application
$application = new Application\Bootstrap($options);
$application->bootstrap();