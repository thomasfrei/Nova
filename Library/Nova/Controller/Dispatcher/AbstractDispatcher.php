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

namespace Nova\Controller\Dispatcher;

use Nova\Controller\Response\AbstractResponse as Response;

/**
 * Abtsract Dispatcher
 * 
 * @package  Nova\Controller
 * @subpackage  Dispatcher
 */
Abstract Class AbstractDispatcher
{
	/**
	 * Response
	 *
	 * @var Response
	 */
	protected $_response = null;

	/**
	 * Module Directory
	 *
	 * @var string
	 */
	protected $_moduleDir = null;

	/**
	 * Controller Directory
	 *
	 * @var string
	 */
	protected $_controllerDir = null;
	/**
	 * Set Response
	 *
	 * @param Response $response
	 * @return AbstractDispatcher
	 */
	public function setResponse(Response $response)
	{
		$this->_response = $response;
		return $this;
	}

	/**
	 * Set the module directory
	 *
	 * @param string
	 * @return AbstractDispatcher
	 */
	public function setModuleDirectory($dir)
	{
		if(is_dir($dir)){
			$this->_moduleDir = $dir;
		}

		return $this;
	}

	/**
	 * Get the module directory
	 *
	 * @return string 
	 */
	public function getModuleDirectory()
	{
		if($this->_moduleDir === null){
			$this->_moduleDir = 'Modules';
		}
		return $this->_moduleDir;
	}

	/**
	 * Set the controller directory
	 *
	 * @param string
	 * @return AbstractDispatcher
	 */
	public function setControllerDirectory($dir)
	{
		if(is_dir($dir)){
			$this->_controllerDir = $dir;
		}

		return $this;
	}

	/**
	 * Get the controller directory
	 *
	 * @return string 
	 */
	public function getControllerDirectory()
	{
		if($this->_controllerDir === null){
			$this->_controllerDir = "Controller";
		}
		
		return $this->_controllerDir;
	}
}