<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     Controller
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace Nova\Controller;

use Nova\Http\Request as Request;
use Nova\Http\AbstractRequest as AbstractRequest;

use Nova\Http\Response as Response;
use Nova\Http\AbstractResponse as AbstractResponse;

use Nova\Controller\Router\Standard as Router;
use Nova\Controller\Router\RouterInterface as RouterInterface;

use Nova\Controller\Dispatcher\Standard as Dispatcher;
use Nova\Controller\Dispatcher\AbstractDispatcher as AbstractDispatcher;

use Nova\Controller\PluginHandler as PluginHandler; 
use Nova\Controller\Plugins\AbstractPlugin as AbstractPlugin; 


/**
 * Front Controller
 *
 * @package     Controller
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Class Front
{
    /**
     * Singleton Instance
     * @var Front
     */
    protected static $_instance = null;
    /**
     * Instance Of AbstractRequest
     * @var AbstractRequest
     */
    protected $_request = null;

    /**
     * Instance Of AbstractResponse
     * @var AbstractResponse
     */
    protected $_response = null;

    /**
     * Instance of Router
     * @var RouterInterface
     */
    protected $_router = null;

    /**
     * Instance of AbstractDispatcher
     * @var AbstractDispatcher
     */
    protected $_dispatcher = null;

    /**
     * Throw Exceptions Flag
     * @var boolean
     */
    protected $_throwExceptions = false;

    /**
     * Get the Singleton Instance
     * 
     * @return Front
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Resets the Front Controller Singleton Instance
     * @return void
     */
    public function resetinstance()
    {
        $reflection = new \ReflectionObject($this);
        foreach ($reflection->getProperties() as $property) {
            $name = $property->getName();
            $this->$name = null;
        }
    }

    /**
     * Constructor
     *
     * Instantiates Plugin Handler
     *
     * @return void
     */
    public function __construct()
    {
        $this->_plugins = new PluginHandler();
    }

    /**
     * Set the Request
     *
     * Accepts a class name or an Instance of AbstractRequest
     * 
     * @param string | AbstractRequest
     * @return Front
     * @throws Exception If Request Does Not Extend AbstractRequest
     */
    public function setRequest($request)
    {
        if (is_string($request)){
            $request = new $request();
        }

        if (!$request instanceof AbstractRequest) {
            throw new Exception("Request Does Not Extend AbstractRequest");
        }

        $this->_request = $request;
        return $this;
    }

    /**
     * Get the Request
     *
     * Instantiates Nova\Http\Request if no Request is currently set
     * 
     * @return AbstractRequest
     */
    public function getRequest()
    {
        if ($this->_request === null) {
            $this->_request = new Request();
        }

        return $this->_request;
    }

    /**
     * Set the Response
     *
     * Accepts a class name or an Instance of AbstractResponse
     * 
     * @param string | AbstractResponse $response
     * @return Front
     * @throws Exception If Response Does not Extend AbstractResponse
     */
    public function setResponse($response)
    {
        if (is_string($response)) {
            $response = new $response();
        }

        if (!$response instanceof AbstractResponse) {
            throw new Exception('Response Does Not Extend AbstractResponse');
        }

        $this->_response = $response;
        return $this;
    }

    /**
     * Get the Response
     *
     * Instantiates Nova\Http\Response if no Response is Currently set
     * 
     * @return AbstractResponse
     */
    public function getResponse()
    {
        if ($this->_response === null) {
            $this->_response = new Response();
        }

        return $this->_response;
    }

    /**
     * Sets the Router
     *
     * The Router is Responsible for matching a Request to 
     * to a Module, Controller and Action.
     * 
     * Accept Classname or instance of RouterInterface
     * 
     * @param RouterInterface $router Instance of Router Interface
     * @return Front
     * @throws Exception if Router Does Not Implement RouterInterface
     */
    public function setRouter($router)
    {
        if (is_string($router)) {
            $router = new $router();
        }

        if (!$router instanceof RouterInterface) {
            throw new Exception('Router Does Not Implement RouterInterface');
        }

        $this->_router = $router;
        return $this;
    }

    /**
     * Get the Router
     *
     * Instantiates Nova\Controller\Router\Standard if no Router is Currently Set
     *     
     * @return RouterInterface 
     */
    public function getRouter()
    {
        if ($this->_router === null) {
            $this->_router = new Router();
        }

        return $this->_router;
    }

    /**
     * Set the Dispatcher
     *
     * The Dispatcher is Responsible for Instantiating 
     * A Controller and the Action Method of the Controller.
     * 
     * Accepts a Classname or an instance of AbstractDispatcher
     * 
     * @param AbstractDispatcher $dispatcher Instance of AbstractDispatcher
     * @return Front
     * @throws Exception If Dispatcher Does not Extend AbstractDispatcher
     */
    public function setDispatcher($dispatcher)
    {
        if (is_string($dispatcher)) {
            $dispatcher = new $dispatcher();
        }

        if (!$dispatcher instanceof AbstractDispatcher) {
            throw new Exception('Dispatcher Does Not Extend AbstractDispatcher');
        }

        $this->_dispatcher = $dispatcher;
        return $this;
    }

    /**
     * Gets the Dispatcher
     *
     * Instantiates Nova\Controller\Dispatcher\Standard if no Dispatcher is Currently set
     * @return AbstractDispatcher
     */
    public function getDispatcher()
    {
        if ($this->_dispatcher === null) {
            $this->_dispatcher = new Dispatcher();
        }

        return $this->_dispatcher;
    }

    /**
     * Sets the Module Directory in the Dispatcher
     * 
     * @param string $moduleDirectory Module Directory
     * @return Front
     */
    public function setModuleDirectory($moduleDirectory)
    {
        $this->getDispatcher()->setModuleDirectory($moduleDirectory);
        return $this;
    }

    /**
     * Gets the Module Directory from the Dispatcher
     * 
     * @return string Module Directory
     */
    public function getModuleDirectory()
    {
        return $this->getDispatcher()->getModuleDirectory();
    }

    /**
     * Register a plugin with the plugin handler
     * 
     * @param  AbstractPlugin $plugin Instance of the plugin    
     * @param  int $index index
     * @return Front
     */
    public function registerPlugin($plugin, $index = null)
    {
        $this->_plugins->registerPlugin($plugin, $index);
        return $this;
    }

    /**
     * Unregister a plugin with the plugin handler
     * 
     * @param  string|AbstractPlugin $plugin Plugin name or Instance of the plugin
     * @return Front
     */
    public function unregisterPlugin($plugin)
    {
        $this->_plugins->unregisterPlugin($plugin);
        return $this;
    }

    /**
     * Returns Array of Registered PLugins
     * @return Array
     */
    public function getRegisteredPlugins()
    {
        return $this->_plugins->getRegisteredPlugins();
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
        $this->_plugins->registerPlugin(new Plugins\ErrorHandler(), 100);


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