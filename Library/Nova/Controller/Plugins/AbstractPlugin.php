<?php 
/**
 * Nova - PHP 5 Framework
 *
 * @package     Controller\Plugins
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace Nova\Controller\Plugins;

use Nova\Controller\Plugins\PluginInterface as PluginInterface;
use Nova\Http\AbstractRequest as AbstractRequest;
use Nova\Http\AbstractResponse as AbstractResponse;

/**
 * Base Plugin Class
 *
 * @package     Controller\Plugins
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
abstract class AbstractPlugin implements PluginInterface
{
    /**
     * Request Object
     * @var AbstractRequest
     */
    protected $_request = null;

    /**
     * Response Object
     * @var AbstractResponse
     */
    protected $_response = null;

    /**
     * Set the Request Object and Register it with each Plugin
     *
     * @param AbstractRequest
     * @return AbstractPlugin
     */
    public function setRequest(AbstractRequest $request)
    {
        $this->_request = $request;
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
     * @return AbstractPLugin
     */
    public function setResponse(AbstractResponse $response)
    {
        $this->_response = $response;
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
     * @throws Nova\Controller\Plugin\Exception
     */
    public function routeStartup(AbstractRequest $request)
    {}

    /**
     * Called before the Router Shuts down
     *
     * @param AbstractRequest
     * @return void 
     * @throws Nova\Controller\Plugin\Exception
     */
    public function routeShutdown(AbstractRequest $request)
    {}

    /**
     * Called before the action is dispatched by the dispatcher
     *
     * @param AbstractRequest
     * @return void 
     * @throws Nova\Controller\Plugin\Exception
     */
    public function preDispatch(AbstractRequest $request)
    {}

    /**
     * Called after the action is dispatched by the dispatcher
     *
     * @param AbstractRequest
     * @return void 
     * @throws Nova\Controller\Plugin\Exception
     */
    public function postDispatch(AbstractRequest $request)
    {}

    /**
     * Called before the Front Controller enters the dispatch loop
     *
     * @param AbstractRequest
     * @return void 
     * @throws Nova\Controller\Plugin\Exception
     */
    public function dispatchLoopStartup(AbstractRequest $request)
    {}

    /**
     * Called before the Front Controller exits the dispatch loop
     * 
     * @param AbstractRequest
     * @return void 
     * @throws Nova\Controller\Plugin\Exception
     */
    public function dispatchLoopShutdown(AbstractRequest $request)
    {}
}