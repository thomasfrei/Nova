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


namespace Nova\Controller\Plugin;

use Nova\Controller\Plugin\PluginInterface as PluginInterface;
use Nova\Controller\Request\AbstractRequest as AbstractRequest;
use Nova\Controller\Response\AbstractResponse as AbstractResponse;
use Nova\Controller\Front as Front;

/**
 * Handles all registered plugins
 *
 * @package Nova\Controller
 * @subpackage Plugin
 */
class Handler extends AbstractPlugin
{
	/**
	 * Array of registered Plugins
	 * @var array
	 */
	protected $_plugins = array();

	/**
	 * Register a Plugin with an index
	 *
	 * Plugins are run in the order they are added. but it is possible to change that order
	 * by defining a custum index.
	 * 
	 * Plugins can hav an index between 1 an 100.
	 * Index 100 is reserved for the error handler
	 *
	 * @param AbstractPlugin $plugin Instance of the plugin
	 * @param int $index index
	 * @todo Index validation
	 * @return Handler
	 * @throws Nova\Controller\Plugin\Exception
	 */
	public function registerPlugin($plugin, $index = null)
	{
		$index = (int) $index;

		if($index < 0 OR $index > 100) {
			throw new Exception('Index must be set to a value between 0 and 100');
		}

		if(!$plugin instanceof AbstractPlugin){
			throw new Exception("Plugin must extend AbstractPlugin");
		}

		if((array_search($plugin, $this->_plugins, true)) !== false){
			throw new Exception("Plugin already registered");
		}

		// Check if this index is already in use
		if($index){
			if(isset($this->_plugins[$index])){
				throw new Exception("Plugin with index " . $index .  " already registered");
			}

			$this->_plugins[$index] = $plugin;
		} else {
			$index = count($this->_plugins);
			while(isset($this->_plugins[$index])){
				$index++;
			}
			$this->_plugins[$index] = $plugin;
		}

		// Sort the plugins 
		ksort($this->_plugins);

		return $this;
	}

	/**
	 * Unregister a plugin from the plugin stack
	 *
	 * @param string | Nova\Controller\Plugin\AbstractPlugin $plugin Instance or class name
	 * @return Nova\Controller\Plugin\Handler
	 */
	public function unregisterPlugin($plugin)
	{
		if($plugin instanceof AbstractPlugin){
			$key = array_search($plugin, $this->_plugins);
			if($key === false){
				throw new Exception("Plugin never registered");
			}
			unset($this->_plugins[$key]);
		}elseif (is_string($plugin)) {
            foreach ($this->_plugins as $key => $_plugin) {
                $type = get_class($_plugin);
                if ($plugin == $type) {
                    unset($this->_plugins[$key]);
                }
            }
		}
		return $this;
	}

	/**
	 * Returns an array of all registered plugins
	 * @return array Registered plugins
	 */
	public function getRegisteredPlugins()
	{
		return $this->_plugins;
	}

	/**
	 * Set the Request Object and Register it with each Plugin
	 *
	 * @param AbstractRequest
	 * @return Nova\Controller\Plugin\Handler
	 */
	public function setRequest(AbstractRequest $request)
	{
		$this->_request = $request;

		foreach ($this->_plugins as $plugin) {
			$plugin->setRequest($request);
		}

		return $this;
	}

	/**
	 * Get the Request Object
	 *
	 * @return AbstractRequest
	 */
	public function getRequest()
	{
		return $this->_request;
	}

	/**
	 * Set the Response Object and Register it with each Plugin
	 *
	 * @param AbstractResponse
	 * @return Nova\Controller\Plugin\Handler
	 */
	public function setResponse(AbstractResponse $response)
	{
		$this->_response = $response;

		foreach ($this->_plugins as $plugin) {
			$plugin->setResponse($response);
		}

		return $this;
	}

	/**
	 * Get the Response Object
	 *
	 * @return AbstractResponse
	 */
	public function getResponse()
	{
		return $this->_response;
	}

	/**
	 * Called before the Router Starts 
	 *
	 * @param AbstractRequest
	 * @return void 
	 * @throws Nova\Controller\Exception
	 */
	public function routeStartup(AbstractRequest $request)
	{
		foreach ($this->_plugins as $plugin) {
			try {
				$plugin->routeStartup($request);
			} catch (Exception $e) {
				if(Front::getInstance()->throwExceptions()){
					throw new Exception($e->getMessage(). $e->getTraceAsString(), $e->getCode(), $e);
				} else {
					$this->getResponse()->setException($e);
				}
			}
		}
	}

	/**
	 * Called before the Router Shuts down
	 *
	 * @param AbstractRequest
	 * @return void 
	 * @throws Nova\Controller\Exception
	 */
	public function routeShutdown(AbstractRequest $request)
	{
		foreach ($this->_plugins as $plugin) {
			try {
				$plugin->routeShutdown($request);
			} catch (Exception $e) {
				if(Front::getInstance()->throwExceptions()){
					throw new Exception($e->getMessage(). $e->getTraceAsString(), $e->getCode(), $e);
				} else {
					$this->getResponse()->setException($e);
				}
			}
		}
	}

	/**
	 * Called before the action is dispatched by the dispatcher
	 *
	 * @param AbstractRequest
	 * @return void 
	 * @throws Nova\Controller\Exception
	 */
	public function preDispatch(AbstractRequest $request)
	{
		foreach ($this->_plugins as $plugin) {
			try {
				$plugin->preDispatch($request);
			} catch (Exception $e) {
				if(Front::getInstance()->throwExceptions()){
					throw new Exception($e->getMessage(). $e->getTraceAsString(), $e->getCode(), $e);
				} else {
					$this->getResponse()->setException($e);
				}
			}
		}
	}

	/**
	 * Called after the action is dispatched by the dispatcher
	 *
	 * @param AbstractRequest
	 * @return void 
	 * @throws Nova\Controller\Exception
	 */
	public function postDispatch(AbstractRequest $request)
	{
		foreach ($this->_plugins as $plugin) {
			try {
				$plugin->postDispatch($request);
			} catch (Exception $e) {
				if(Front::getInstance()->throwExceptions()){
					throw new Exception($e->getMessage(). $e->getTraceAsString(), $e->getCode(), $e);
				} else {
					$this->getResponse()->setException($e);
				}
			}
		}
	}

	/**
	 * Called before the Front Controller enters the dispatch loop
	 *
	 * @param AbstractRequest
	 * @return void 
	 * @throws Nova\Controller\Exception
	 */
	public function dispatchLoopStartup(AbstractRequest $request)
	{
		foreach ($this->_plugins as $plugin) {
			try {
				$plugin->dispatchLoopStartup($request);
			} catch (Exception $e) {
				if(Front::getInstance()->throwExceptions()){
					throw new Exception($e->getMessage(). $e->getTraceAsString(), $e->getCode(), $e);
				} else {
					$this->getResponse()->setException($e);
				}
			}
		}
	}

	/**
	 * Called before the Front Controller exits the dispatch loop
	 *
	 * @param AbstractRequest $request
	 * @return void 
	 * @throws Nova\Controller\Plugin\Exception
	 */
	public function dispatchLoopShutdown(AbstractRequest $request)
	{
		foreach ($this->_plugins as $plugin) {
			try {
				$plugin->dispatchLoopShutdown($request);
			} catch (Exception $e) {
				if(Front::getInstance()->throwExceptions()){
					throw new Exception($e->getMessage(). $e->getTraceAsString(), $e->getCode(), $e);
				} else {
					$this->getResponse()->setException($e);
				}
			}
		}
	}
}