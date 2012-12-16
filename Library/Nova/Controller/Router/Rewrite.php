<?php 
/**
 * Nova - PHP 5 Framework
 *
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2012 Thomas Frei
 * @link        https://github.com/thomasfrei/nova
 * @license     https://github.com/thomasfrei/nova/blob/master/License.txt 
 * @package     Nova\Controller\Router
 * @version     0.0.1 
 *
 */
namespace Nova\Controller\Router;

use Nova\Controller\Request\AbstractRequest as AbstractRequest;
use Nova\Controller\Dispatcher\Standard as Standard;

/**
 * Routes a Request to coresponding Module/Controller/Action
 *
 * @todo  Implement Route Matching
 * @package  Nova\Controller
 * @subpackage Router
 */
Class Rewrite
{
	/**
	 * Instance of Dispatcher
	 * @var Standard
	 */
	protected $_dispatcher = null;

	/**
	 * Instance of AbstractRequest
	 * @var  AbstractRequest
	 */
	protected $_request = null;
	/**
	 * Action params
	 * @var array
	 */
	protected $_params = array();

	/**
	 * Module detected from request
	 * @var string
	 */
	protected $_module = null;

	/**
	 * Controller detected from request
	 * @var string
	 */
	protected $_controller = null;

	/**
	 * Action detected from request
	 * @var string
	 */
	protected $_action = null;

	/**
	 * Holds the segments of the rewrite base
	 * @var array 
	 */
	protected $_parts = array();

	/**
	 * Route a Request
	 *
	 * @param  AbstractRequest $request
	 * @return Rewrite       
	 */
	public function route(AbstractRequest $request)
	{
		// Set the Request
		$this->setRequest($request);

		// Instantiate Default Dispatcher
		$this->getDispatcher();

		// Get the rewrite base
		$base = $this->getRewriteBase();

		// index.php/
		if($base === '/' || empty($base)){
			$this->_request->setModuleName($this->getDefaultModule());
			$this->_request->setControllerName($this->getDefaultController());
			$this->_request->setActionName($this->getDefaultAction());
		} else {
			$base = rtrim($base, "/");
			$this->_parts = explode('/', $base);

			if($this->_parts[0] === ''){
				array_shift($this->_parts);	
			}
			// Check for Module			
			$this->_setModule();
		
			// Check for Controller
			$this->_setController();

			// Check for action
			$this->_setAction();

			// Check for Params
			$params = array();

			for ($i=0; $i<sizeof($this->_parts); $i=$i+2) {
            	$params[$this->_parts[$i]] = isset($this->_parts[$i+1]) ? $this->_parts[$i+1] : null;
        	}
        	$this->_params = $params;
		}
		
		$this->_request->setActionParams($this->_params);
		return $this;
	}

	/**
	 * sets the action detected from the request
	 */
	protected function _setAction()
	{
		if(isset($this->_parts[0])){
			$this->_action = $this->_parts[0];
		} else {
			$this->_action = $this->_dispatcher->getDefaultAction();
		}
		$this->_request->setActionName($this->_action);
		array_shift($this->_parts);
	}

	/**
	 * Sets the controller detected from the request
	 */
	protected function _setController()
	{
		if(isset($this->_parts[0])){
			$this->_controller = $this->_parts[0];
		} else {
			$this->_controller = $this->_dispatcher->getDefaultController();
		}

		$this->_request->setControllerName($this->_controller);
		array_shift($this->_parts);
	}

	/**
	 * Sets the module detected from the request
	 */
	protected function _setModule()
	{
		if(isset($this->_parts[0])){
			if(is_dir(APPPATH . ucfirst(strtolower($this->_parts[0])))){
				$this->_module = $this->_parts[0];
				array_shift($this->_parts);
			} else {
				$this->_module = $this->getDefaultModule();
			}
		} else {
			$this->_module = $this->getDefaultModule();
		}

		$this->_request->setModuleName($this->_module);
	}

	/**
	 * Get the rewrite base
	 * 
	 * Uses the baseUri and the reqeuestUri to determin
	 * the rewrite base
	 * 
	 * E.g.:  
	 *   
	 *   - /site/home/ (baseUri)
	 *   - /site/home/news/item/35/ (requestUri)
	 *   
	 * Would make the rewrite base - news/item/35
	 * 
	 * @return [type] [description]
	 */
	public function getRewriteBase()
	{
		// Get The Request Uri
		$requestUri = $this->_request->getRequestUri();

		// Get The Base Uri
		$baseUri = $this->_request->getBaseUri();

		/**
		 * Compare RequestUri with BaseUri
		 * with index.php in uri
		 */
		$pos = strpos($baseUri, $requestUri);

		if (strpos($requestUri, $baseUri) === 0) {
                    $base = substr($requestUri, strlen($baseUri));
		} else {
			// Without index.php in Uri
			$baseDir = dirname($baseUri);
			$pos = strpos($baseDir, $requestUri);

			if (strpos($requestUri, $baseDir) === 0) {
                    $base = substr($requestUri, strlen($baseDir));
			}
		}
		return $base;
	}

	/**
	 * Set the Request
	 * 
	 * @param AbstractRequest $request 
	 */
	public function setRequest(AbstractRequest $request)
	{
		$this->_request = $request;
	}

	/**
	 * Sets the Dispatcher to use
	 *
	 * If no Dispatcher is passed, Instantiates default dispatcher
	 * 
	 * @param null|AbstractDispatcher $dispatcher
	 * @return Rewrite
	 */
	public function setDispatcher(AbstractDispatcher $dispatcher = null)
	{
		if($dispatcher !== null){
			if(!$dispatcher instanceof AbstractDispatcher){
				throw new Excception("Invalid Dispatcher. Dispatcher needs to extend AbstractDispatcher");
			}
			$this->_dispatcher = $dispatcher;
		} else {
			$this->_dispatcher = new Standard();
		}

		return $this;
	}

	/**
	 * Retrieve the Dispatcher
	 * 
	 * @return AbstractDispatcher
	 */
	public function getDispatcher()
	{
		if($this->_dispatcher === null){
			$this->setDispatcher();
		}

		return $this->_dispatcher;
	}

	/**
	 * Gets the default module from the Dispatcher
	 * 
	 * @return string Name of the default module
	 */
	public function getDefaultModule()
	{
		return $this->getDispatcher()->getDefaultModule();
	}

	/**
	 * Gets the default controller from the Dispatcher
	 * 
	 * @return string Name of the default controller
	 */
	public function getDefaultController()
	{
		return $this->getDispatcher()->getDefaultController();
	}

	/**
	 * Gets the default action from the Dispatcher
	 * 
	 * @return string Name of the default action
	 */
	public function getDefaultAction()
	{
		return $this->getDispatcher()->getDefaultAction();
	}
}