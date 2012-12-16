<?php 
/**
 * Nova - PHP 5 Framework
 *
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2012 Thomas Frei
 * @link        https://github.com/thomasfrei/nova
 * @license     https://github.com/thomasfrei/nova/blob/master/License.txt 
 * @package     Nova_Controller
 * @version     0.0.1 
 *
 */
namespace Nova\Controller;

use Nova\Controller\Request\Http as Request;
use Nova\Controller\Request\AbstractRequest as AbstractRequest;
use Nova\Controller\Response\Http as Response;
use Nova\Controller\Response\AbstractResponse as AbstractResponse;
use Nova\Controller\Dispatcher\Standard as Dispatcher;
use Nova\Controller\Dispatcher\AbstractDispatcher as AbstractDispatcher;
use Nova\Controller\Router\Rewrite as Router;
use Nova\Controller\Plugin\Handler as PluginHandler; 

/**
 * Is Responsible for the Request->Response Lifecycle
 * 
 * @package  Nova\Controller
 */
Class Front
{
	/**
	 * Singleton Instance
	 * @var Front
	 */
	protected static $_instance = null;

	/**
	 * Instance of AbstractRequest
	 * @var AbstractRequest
	 */
	protected $_request = null;

	/**
	 * Instance of AbstractResponse
	 * @var AbstractResponse
	 */
	protected $_response = null;

	/**
	 * Instance of Router
	 * @todo Router Interface or Abstract
	 */
	protected $_router = null;

	/**
	 * Base Url
	 * @var string
	 */
	protected $_baseUrl = null;

	/**
	 * Throw Exceptions Flag
	 * @var boolean
	 */
	protected $_throwExceptions = false;

	/**
	 * Instance of AbstractDispatcher
	 * @var AbstractDispatcher
	 */
	protected $_dispatcher = null;

	/**
	 * Controller Directory
	 * @var string
	 */
	protected $_controllerDir = "/Controller/";

	/**
	 * Array of params
	 * @var array
	 */
	protected $_params = array();

	/**
	 * Array of registered Plugins
	 * @var [type]
	 */
	protected $_plugins = null;

	/**
	 * Contructor
	 *
	 * Instatiates the Pluginhandler
	 */
	public function __construct()
	{
		$this->_plugins = new PluginHandler();
	}

	/**
	 * Singleton Instance
	 * 
	 * @return Front
	 */
	public static function getInstance()
	{
		if(self::$_instance == null){
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Set The Request
	 * 
	 * @param string | AbstractRequest
	 * @return Front
	 * @throws Nova\Controller\Exception if invalid Request Object
	 */
	public function setRequest($request)
	{
		if(is_string($request)){
			$request = new $request();
		}

		if(!$request instanceof AbstractRequest){
			throw new Exception("Invalid Request Object");
		}

		$this->_request = $request;
		return $this;

	}

	/**
	 * Rerieve the Request
	 *
	 * @return AbstractRequest
	 */
	public function getRequest()
	{
		return $this->_request;
	}

	/**
	 * Set the Response
	 *
	 * @param string | AbstractResponse
	 * @return Front
	 * @throws Nova\Controller\Exception if invalid Response Object
	 */
	public function setResponse($response)
	{
		if(is_string($response)){
			$response = new $response;
		}

		if(!$response instanceof AbstractResponse){
			throw new Exception("Invalid Response Object");
		}

		$this->_response = $response;
		return $this;
	}

	/**
	 * Retrieve the Response
	 *
	 * @return Nova\Controller\Response\AbstractResponse
	 */
	public function getResponse()
	{
		return $this->_response;
	}

	/**
	 * Set the Dispatcher
	 *
	 * @param string | AbstractDispatcher
	 * @return Front
	 * @throws Nova\Controller\Exception if invalid Dispatcher
	 */
	public function setDispatcher($dispatcher = null)
	{	
		if($dispatcher === null){
			$dispatcher = new Dispatcher();
		} elseif (is_string($dispatcher)) {
			$dispatcher = new $dispatcher();
		}

		if(!$dispatcher instanceof AbstractDispatcher){
			throw new Front\Exception("Invalid Dispatcher");
		}

		$this->_dispatcher = $dispatcher;
		return $this;
	}

	/**
	 * Retrieve the Dispatcher
	 * If none is set calls set setDispatcher method
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
	 * Set The Router
	 *
	 * @param string | Router
	 * @return Nova\Controller\Front
	 */
	public function setRouter($router = null)
	{
		if($router === null){
			$router = new Router();
		} elseif (is_string($router)) {
			$router = new $router();
		}

		$this->_router = $router;
		return $this;
	}

	/**
	 * Retrieve the Router
	 * If none is Set calls the setRouter method
	 *
	 * @return Router
	 */
	public function getRouter()
	{
		if($this->_router === null){
			$this->setRouter();
		}
		return $this->_router;
	}

	/**
	 * Set the module Directory
	 *
	 * @param string
	 * @return Front
	 */
	public function setModuleDirectory($dir)
	{
		$this->getDispatcher()->setModuleDirectory($dir);
		return $this;
	}

	/**
	 * Get the module directory
	 * 
	 * @return string
	 */
	public function getModuleDirectory()
	{
		return $this->getDispatcher()->getModuleDirectory();
	}

	/**
	 * Set or Retrieve the throw Exceptions flag
	 * 
	 * @param  boolean $flag 
	 * @return boolean|Front
	 */
	public function throwExceptions($flag = null)
	{
        if ($flag !== null) {
            $this->_throwExceptions = (bool) $flag;
            return $this;
        }

        return $this->_throwExceptions;		
	}

	/**
	 * Dispatch the Request
	 *
	 * @param AbstractRequest|string
	 * @param AbstractResponse|string
	 */
	public function dispatch(AbstractRequest $request = null, AbstractResponse $response = null)
	{
		// Register ErrorHandler with index of 100
		$this->_plugins->registerPlugin(new Plugin\ErrorHandler(), 100);


		// Instantiate Default Request Object if none provided
		if ($request !== null){
			$this->setRequest($request);			
		} elseif ( ($request === null) && ($request = $this->getRequest()) === null) {
			$request = new Request();
			$this->setRequest($request);
		}

		// Instantiate Default Response Object if none Provided
		if ($response !== null){
			$this->setResponse($response);
		} elseif ( ($response === null) && ($response = $this->getResponse()) === null) {
			$response = new Response();
			$this->setResponse($response);
		}

		// Instatiate Dispatcher
		if($this->_dispatcher === null){
			$this->getDispatcher();
		}

		// Instatiate Router
		if($this->_router === null){
			$this->getRouter();
		}

		$this->_plugins->setRequest($request)
						->setResponse($response);
	

		// Start Dispatch
		try {

			// plugins route Startup
			$this->_plugins->routeStartup($this->_request);

			try {
				// Route the Request
				$this->_router->route($this->_request);
			} catch (Exception $e) {
				if($this->throwExceptions()){
					throw $e;
				}

				$this->_response->setException($e);
			}

			// Plugins route shutddown
			$this->_plugins->routeShutdown($this->_request);

			// plugins Dispatch loop Startup
			$this->_plugins->dispatchLoopStartup($this->_request);

			do {
				// Set dispatched to True
				$this->_request->setDispatched(true);

				// Plugins predispatch
				$this->_plugins->preDispatch($this->_request);

				// Dispatch request
				try {
					$this->_dispatcher->dispatch($this->_request, $this->_response);
				} catch (Exception $e) {
					if($this->throwExceptions()){
						throw $e;
					}
					$this->_response->setException($e);
				}

				// plugins post Dispatch
				$this->_plugins->postDispatch($this->_request);

			} while (!$this->_request->isDispatched());

		} catch (Exception $e) {
			if($this->throwExceptions()){
				throw $e;
			}
			$this->_response->setException($e);
		}

		try {
			$this->_plugins->dispatchLoopShutdown($this->_request);
		} catch (Exception $e) {
			if($this->throwExceptions()){
				throw $e;
			}
			$this->_response->setException($e);
		}

		// Aaaaaaaaaaaaaaan were done
		$this->_response->sendResponse();
	}
}