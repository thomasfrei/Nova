<?php 
/**
 * Nova - PHP 5 Framework
 *
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2012 Thomas Frei
 * @link        https://github.com/thomasfrei/nova
 * @license     https://github.com/thomasfrei/nova/blob/master/License.txt 
 * @package     Nova\Controller
 * @version     0.0.1 
 * */

namespace Nova\Controller\Dispatcher;

use Nova\Controller\Request\Http as Request;
use Nova\Controller\Response\Http as Response;
use Nova\Controller\Action as Action;

/**
 * Standard dispatcher
 *
 * @package Nova\Controller
 * @subpackage Dispatcher
 */
Class Standard extends AbstractDispatcher
{
	/**
	 * Default module
	 * @var string
	 */
	public $_defaultModule = 'default';

	/**
	 * Default Controller
	 * @var string
	 */
	public $_defaultController = 'index';

	/**
	 * Default action
	 * @var string
	 */
	public $_defaultAction = 'index';

	/**
	 * Formats the module name 
	 *
	 * @param string $unformatted unformatted module name
	 * @return string $formatted formatted module name
	 */
	public function formatModuleName($unformatted)
	{
		$formatted= ucfirst(strtolower($unformatted));
		return $formatted;
	}

	/**
	 * Formats the controller name
	 *
	 * @param string $unformatted unformatted controller name
	 * @return string $formatted formatted controller name
	 */
	public function formatControllerName($unformatted)
	{
		$formatted = ucfirst(strtolower($unformatted));
		return $formatted . 'Controller';
	}

	/**
	 * Fromates the action name
	 *
	 * @param string $unformatted  unformatted action name
	 * @return string $formatted action name
	 */
	public function formatActionName($unformatted)
	{
		$formatted = strtolower($unformatted);
		return $formatted . 'Action';
	}

	/**
	 * Returns the default module
	 *
	 * @return string 
	 */
	public function getDefaultModule()
	{
		return $this->_defaultModule;
	}

	/**
	 * Changes the default module
	 *
	 * @param string $defaultModule Default module to use
	 * @return Standard
	 */
	public function setDefaultModule($defaultModule)
	{
		$this->_defaultModule = $defaultModule;
		return $this;
	}

	/**
	 * Returns the default controller
	 *
	 * @return string 
	 */
	public function getDefaultController()
	{
		return $this->_defaultController;
	}

	/**
	 * Changes the default controller
	 *
	 * @param string $defaultController Default controller to use
	 * @return void
	 */
	public function setDefaultController($defaultController)
	{
		$this->_defaultController = $defaultController;
		return $this;
	}

	/**
	 * Returns the default action
	 *
	 * @return string 
	 */
	public function getDefaultAction()
	{
		return $this->_defaultAction;
	}

	/**
	 * Change the default action 
	 *
	 * @param string $defaultAction Default action to use
	 * @return  Standard
	 */
	public function setDefaultAction($defaultAction)
	{
		$this->_defaultAction = $defaultAction;
		return $this;
	}

	/**
	 * Format classname to Filename
	 *
	 * @param  string Unformatted Classname
	 * @return string Formatted Filename
	 */
	public function classToFilename($class)
	{
		return $class . '.php';
	}

	/**
	 * Checks if the module exists in the  module directory
	 * 	
	 * @param  string  $path   Path to the module directory
	 * @param  string  $module Name of the module
	 * @return boolean         
	 */
	public function isValidModule($path, $module)
	{
		return (!is_dir($path . $module)) ? true : false;
	}

	/**
	 * Dispatch the request 
	 * 
	 * @param  Request  $request Request Object
	 * @param  Response $response Response Object
	 * @throws Nova\Controller\Dispatcher\Exception
	 * @return Void
	 */
	public function dispatch(Request $request, Response $response)
	{
		$this->setResponse($response);

		// path to modules
		$path = $this->getModuleDirectory();
		$module = $this->formatModuleName($request->getModuleName());

		if($this->isValidModule($path, $module)){
			throw new Exception("Invalid Module " . $module);
		}

		// path to controller file
		$path = $path . $module .DIRECTORY_SEPARATOR;

		$controller = $this->formatControllerName($request->getControllerName());
		$file = $this->classToFilename($controller);
		$action = $this->formatActionName($request->getActionName());


		if(!$this->isDispatchable($path, $file)){
			$request->setDispatched(false);
			throw new Exception("File: " . $file . " was not found");
			return;
		}

		include_once($path.$this->getControllerDirectory().DIRECTORY_SEPARATOR.$file);

		if(!class_exists($controller,false)){
			throw new Exception("Class: " . $controller . " was not found");			
		}
		$controllerClass = new $controller($request, $response);

		if(!$controllerClass instanceof Action){
			throw new Exception("Controller must extend Nova\Controller\Action");
		}

		// check if the method exists
		$actionMethods = get_class_methods($controllerClass);

		// Method does not exist
		if(!in_array($action, $actionMethods)){
			$action = substr($action, 0 , strlen($action) - 6);
			throw new Exception($action.'Action does not exist in '.$controller);
		}

		// Call action
		$controllerClass->$action();

		$request->setDispatched(true);
	}

	/**
	 * Checks if Controller exists in the module Directory
	 * 
	 * @param  string  $path path to the Controller File
	 * @param  string  $file name of the Controller File
	 * @return boolean
	 */
	public function isDispatchable($path, $file)
	{
		// Check if the module and Controller Exist
		if(!is_dir($path) || !file_exists($path.$this->getControllerDirectory().DIRECTORY_SEPARATOR.$file)){
			return false;
		}

		return true;
	}

}