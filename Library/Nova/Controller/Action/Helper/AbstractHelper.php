<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     Controller\Action\Helpers    
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace Nova\Controller\Action\Helper;

use Nova\Controller\Action as Action;

/**
 * Abstract Helper Base Class
 *
 * @package     Controller\Action\Helpers      
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
 Abstract Class AbstractHelper
 {
 	/**
	 * Instance of the Action Controller
	 * @var Action
	 */
	protected $_actionController;

	/**
	 * Set the Action Controller
	 * 
	 * @param Action $actionController 
	 * @return AbstractHelper
	 */
	public function setActionController($actionController = null)
	{
		$this->_actionController = $actionController;
		return $this;
	}

	/**
	 * Returns the Action Controller
	 * 
	 * @return Action
	 */
	public function getActionController()
	{
		return $this->_actionController;
	}

	/**
	 * Returns the Instance of the Front Controller
	 * 
	 * @return \Nova\Controller\Front
	 */
	public function getFrontController()
	{
		return \Nova\Controller\Front::getInstance();
	}

	/**
     * Initialises Action Helpers
     * 
     * @return void
     */
	public function init()
	{}

	/**
     * Runs before the Action Controller has been Dispatched
     * 
     * @return void
     */
	public function preDispatch()
	{}

	/**
     * Runs after the Action Controller has been Dispatched
     * 
     * @return void
     */
	public function postDispatch()
	{}

	/**
	 * Returns the Request
	 * 
	 * @return \Nova\Http\AbstractRequest
	 */
	public function getRequest()
	{
		$controller = $this->getActionController();
		if ($controller == null) {
			$controller = $this->getFrontController();
		}

		return $controller->getRequest();
	}

	/**
	 * Returns the Response
	 * 
	 * @return \Nova\Http\AbstractResponse
	 */
	public function getResponse()
	{
		$controller = $this->getActionController();
		if ($controller == null) {
			$controller = $this->getFrontController();
		}

		return $controller->getResponse();
	}
 }