<?php
/**
 * Nova - PHP 5 Framework
 *
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2012 Thomas Frei
 * @link        https://github.com/thomasfrei/nova
 * @license 	https://github.com/thomasfrei/nova/blob/master/License.txt 
 * @package     Nova\Loader
 * @version     0.0.1 
 */

namespace Nova\Loader;

/**
 * Autoloader
 *
 * This autoloader loads both namespaced and vendor-prefixed classes
 *
 * @package  Nova\Loader
 * @subpackage Autoloader
 */
Class Autoloader
{
	/**
	 * Extension of the file
	 * @var string
	 */
	protected $_fileExtension = '.php';

	/**
	 * Registers the Autoloader with the SPL stack
	 *  
	 * @param  boolean $throw   Whether spl_autoload_register should throw Exceptions when the autoload function cannot be registered
	 * @param  boolean $prepend If true, spl_autoload_register() will prepend the autoloader on the autoload stack instead of appending it.
	 * @return void
	 */
	public function registerAutoloader($throw = false, $prepend = false)
	{
		spl_autoload_register(array($this, '_loadClass'), (bool) $throw, (bool) $prepend);
	}

	/**
	 * Remove the Autoloader from the SPL stack
	 *
	 * @return void
	 */
	public function unregisterAutoloader()
	{
		spl_autoload_unregister(array($this, '_loadClass'));
	}

	/**
	 * Sets the include path
	 * 
	 * @param string|array $path string or array of paths
	 */
	public function setIncludePath($path)
	{
		if(is_array($path)){
			$path = implode(':', $path);
		}

		set_include_path($path);
	}

	/**
	 * Checks if the class is already loaded
	 * 
	 * @param  string  $classname name of the Class
	 * @return boolean            
	 */
	protected function _isLoaded($classname)
	{
		return (class_exists($classname, false));
	}

	/**
	 * Transforms a class name with namespaces to a filename with directory separators and php extension
	 * 
	 * @param  string $classname Name of the Class
	 * @return string $filename Name of the File
	 */
	protected function _classToFilename($classname)
	{
		defined('NAMESPACE_SEPARATOR') or define('NAMESPACE_SEPARATOR', '\\');
		
		
		$classname = ltrim($classname, '\\');
		$filename = str_replace(NAMESPACE_SEPARATOR, DIRECTORY_SEPARATOR, $classname);

		return $filename . $this->_fileExtension;
	}

	/**
	 * Autoloads a Class from the Include Path
	 * 
	 * @param  string $classname Name of the class
	 * @return bool success or failure 	
	 */
	protected function _loadClass($classname)
	{
		if($this->_isLoaded($classname)){
			return true;
		}

		$file = $this->_classToFilename($classname);
		
		if(!stream_resolve_include_path($file)){
			return false;
		}

		require_once($file);
		return true;
	}

}