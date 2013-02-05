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

use Nova\Http\AbstractRequest as AbstractRequest;
use Nova\Http\AbstractResponse as AbstractResponse;

/**
 * Plugin Interface
 *
 * @package     Controller\Plugins
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

interface PluginInterface
{

    /**
     * Set the Request Object and Register it with each Plugin
     *
     * @param AbstractRequest
     * @return Nova\Controller\Plugin\Handler
     */
    public function setRequest(AbstractRequest $request);


    /**
     * Get the Request Object
     *
     * @return AbstractRequest
     */
    public function getRequest();


    /**
     * Set the Response Object and Register it with each Plugin
     *
     * @param AbstractResponse
     * @return Nova\Controller\Plugin\Handler
     */
    public function setResponse(AbstractResponse $response);


    /**
     * Get the Response Object
     *
     * @return Nova\Controller\Response\AbstractResponse
     */
    public function getResponse();


    /**
     * Called before the Router Starts 
     *
     * @param AbstractRequest
     * @return void 
     * @throws Nova\Controller\Exception
     */
    public function routeStartup(AbstractRequest $request);

    /**
     * Called before the Router Shuts down
     *
     * @param AbstractRequest
     * @return void 
     * @throws Nova\Controller\Exception
     */
    public function routeShutdown(AbstractRequest $request);


    /**
     * Called before the action is dispatched by the dispatcher
     *
     * @param AbstractRequest
     * @return void 
     * @throws Nova\Controller\Exception
     */
    public function preDispatch(AbstractRequest $request);


    /**
     * Called after the action is dispatched by the dispatcher
     *
     * @param AbstractRequest
     * @return void 
     * @throws Nova\Controller\Exception
     */
    public function postDispatch(AbstractRequest $request);


    /**
     * Called before the Front Controller enters the dispatch loop
     *
     * @param AbstractRequest
     * @return void 
     * @throws Nova\Controller\Exception
     */
    public function dispatchLoopStartup(AbstractRequest $request);


    /**
     * Called before the Front Controller exits the dispatch loop
     *
     * @param AbstractRequest
     * @return void 
     * @throws Nova\Controller\Exception
     */
    public function dispatchLoopShutdown(AbstractRequest $request);
}