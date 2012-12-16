<?php
/**
 * Nova - PHP 5 Framework
 *
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2012 Thomas Frei
 * @link        https://github.com/thomasfrei/nova
 * @license 	https://github.com/thomasfrei/nova/blob/master/License.txt 
 * @package     Nova\Controller
 * @version     0.0.1 
 */

namespace Nova\Controller\Request;

/**
 * Abstract Request
 *
 * This is the base class for handling requests. 
 * It provides basic setters and getters for request params
 *
 * Extend this class when you need the request class to interact with
 * a specific environment. Examples: HTTP, Cli, PHP-GTK
 *
 * @package 		Nova\Controller
 * @subpackage 		Request
 */
Abstract Class AbstractRequest
{
	/**
	 * Name of the requested module
	 * @var string
	 */
	protected $_moduleName;

	/**
	 * Key for retrieving module name fron params
	 * @var string
	 */
	protected $_moduleKey = 'module';

	/**
	 * Name of the requested controller
	 * @var string
	 */
	protected $_controllerName;

	/**
	 * Key for retrieving controller name from params
	 * @var string
	 */
	protected $_controllerKey = 'controller';

	/**
	 * Name of the requested action
	 * @var string
	 */
	protected $_actionName;

	/**
	 * Key for retrieving action name from params
	 * @var string
	 */
	protected $_actionKey = 'action';

	/**
	 * Flag: Has the request been dispatched
	 * @var boolean
	 */
	protected $_dispatched = true;

	/**
	 * Array of request params
	 * @var array
	 */
	protected $_params = array();

	/**
	 * Returns the module name 
	 * 
	 * @return string Name of the module
	 */
	public function getModuleName()
	{
		if($this->_moduleName === null){
			$this->_moduleName = $this->getParam($this->getModuleKey());
		}

		return $this->_moduleName;
	}

	/**
	 * Sets the module name
	 * 
	 * @param string $moduleName Name of the module
	 * @return AbstractRequest
	 */
	public function setModuleName($moduleName)
	{
		$this->_moduleName = $moduleName;
		$this->setParam($this->getModuleKey(), $this->_moduleName);

		return $this;
	}

	/**
	 * returns the controller name
	 * 
	 * @return string Name of the controller
	 */
	public function getControllername()
	{
		if($this->_controllerName === null){
			$this->_controllerName = $this->getParam($this->getControllerKey());
		}

		return $this->_controllerName;
	}

	/**
	 * Sets the controller name
	 * 
	 * @param string $controllerName Name of the controller
	 * @return AbstractRequest
	 */
	public function setControllerName($controllerName)
	{
		$this->_controllerName = $controllerName;
		$this->setParam($this->getControllerKey(), $this->_controllerName);

		return $this;
	}

	/**
	 * Returns the action name
	 * 
	 * @return string Name of the action
	 */
	public function getActionName()
	{
		if($this->_actionName === null){
			$this->_actionName = $this->getParam($this->getActionKey());
		}

		return $this->_actionName;
	}

	/**
	 * Sets the action name
	 * 
	 * @param string $actionName name of the action
	 * @return  AbstractRequest
	 */
	public function setActionName($actionName)
	{
		$this->_actionName =  $actionName;
		$this->setParam($this->getActionKey(), $this->_actionName);

		return $this;
	}

	/**
	 * Returns the module key.
	 * Used to identify the module in the params array
	 * 
	 * @return string Name of the module key
	 */
	public function getModuleKey()
	{
		return $this->_moduleKey;
	}

	/**
	 * Sets the module key
	 * 
	 * @param string $moduleKey name of the module key
	 * @return  AbstractRequest
	 */
	public function setModuleKey($moduleKey)
	{
		$this->_moduleKey = $moduleKey;
		return $this;
	}

	/**
	 * Returns the controller key.
	 * Used to identify the controller in the params array
	 * 
	 * @return string Name of the controller key
	 */
	public function getControllerKey()
	{
		return $this->_controllerKey;
	}

	/**
	 * Sets the controller key
	 * 
	 * @param string $controllerKey name of the controller key
	 * @return  AbstractRequest
	 */
	public function setControllerKey($controllerKey)
	{
		$this->_controllerKey = $controllerKey;
		return $this;
	}

	/**
	 * Returns the action key.
	 * Used to identify the action in the params array
	 * 
	 * @return string Name of the action key
	 */
	public function getActionKey()
	{
		return $this->_actionKey;
	}

	/**
	 * Sets the action key
	 * 
	 * @param string $actionKey name of the action key
	 * @return  AbstractRequest
	 */
	public function setActionKey($actionKey)
	{
		$this->_actionKey = $actionKey;
		return $this;
	}

	/**
	 * Returns an action param. 
	 * Returns default value if the key is not found 
	 * @param  string $key     array key
	 * @param  string $default default value to return if key is not found
	 * @return string value to corresponding key or null if key not found
	 */	
	public function getParam($key, $default = null)
	{
		if(isset($this->_params[$key])){
			return $this->_params[$key];
		}

		return $default;
	}

	/**
	 * Sets an action param. 
	 * A value of null will unset the key if it exists
	 * @param string $key   array key
	 * @param string|null $value value of the key or null to unset existing key
	 * @return  AbstractRequest
	 */
	public function setParam($key, $value)		
	{
		if($value !== null){
			$this->_params[$key] = $value;
		} elseif ( ($value === null) && (isset($this->_params[$key])) ){
			unset($this->_params[$key]);
		}

		return $this;
	}

	/**
	 * Returns the entire params Array
	 * 
	 * @return array Array containing all action params
	 */
	public function getParams()
	{
		return $this->_params;
	}

	/**
	 * Set multiple action params
	 * Null values will unset the associated key if it exists
	 * 
	 * @param array $newParams array of params
	 */
	public function setParams(array $newParams)
	{
		$this->_params = $this->_params + $newParams;

		foreach($newParams as $key => $value){
			if( ($value === null) && (isset($this->_params[$key]))){
				unset($this->_params[$key]);
			}
		}

		return $this;
	}

	/**
	 * Unsets all action parameters
	 * 
	 * @return AbstractRequest
	 */
	public function clearParams()
	{
		$this->_params = null;
		return $this;
	}

	/**
	 * Sets multiple action params. Does not unset key with null values
	 * 
	 * @param array $newParams array of params
	 * @return AbstractRequest
	 */
	public function setActionParams(array $newParams)
	{
		$this->_params = $this->_params + $newParams;

		return $this;
	}

	/**
	 * Sets the dispatched flag
	 * 
	 * @param boolean $flag 
	 * @return AbstractRequest
	 */
	public function setDispatched($flag = true)
	{
		$this->_dispatched = (bool) $flag;
		return $this;
	}

	/**
	 * Has the request been dispatched ?
	 * 
	 * @return boolean
	 */
	public function isDispatched()
	{
		return $this->_dispatched;
	}

}