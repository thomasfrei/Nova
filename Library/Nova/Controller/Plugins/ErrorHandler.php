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

use Nova\Controller\Front as Front;
use Nova\Http\AbstractRequest as AbstractRequest;
use Nova\CHttp\AbstractResponse as AbstractResponse;

/**
 * Description
 *
 * @package     Controller\Plugins
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Class ErrorHandler extends AbstractPlugin
{
    /**
     * Exception type constants
     * @var const
     */
    const DISPATCHER_EXCEPTION = 'DISPATCHER_EXCEPTION';
    const CONTROLLER_EXCEPTION = 'CONTROLLER_EXCEPTION';
    const OTHER_EXCEPTION = 'OTHER_EXCEPTION';

    /**
     * Error Module
     * @var string
     */
    protected $_errorModule = "error";

    /**
     * Error Conrtroller
     * @var string
     */
    protected $_errorController = "error";

    /**
     * Error Action
     * @var string
     */
    protected $_errorAction = "error";

    /**
     * Are wi Inside the Error Handler Loop ?
     * @var boolean
     */
    protected $_handlerLoop = false;

    /**
     * Set the module name of the error handler.
     * 
     * @param string $module Name of the error module
     * @return ErrorHandler
     */         
    public function setErrorModule($module)
    {
        $this->_errorModule = (string) $module;
        return $this;
    }

    /**
     * Get the name of the error handler module.
     * 
     * @return string Name of the error module
     */
    public function getErrorModule()
    {
        return $this->_errorModule;
    }

    /**
     * Set the error controller name.
     * 
     * @param string $controller Name of the Error Controller
     * @return ErrorHandler
     */
    public function setErrorController($controller)
    {
        $this->_errorController = (string) $controller;
        return $this;
    }

    /**
     * Get the name of the error controller.
     * 
     * @return string Name of the error controller
     */
    public function getErrorController()
    {
        return $this->_errorController;
    }

    /**
     * Set the action name of the error handler.
     * 
     * @param string $action Name of the error action
     * @return ErrorHandler
     */
    public function setErrorAction($action)
    {
        $this->_errorAction = (string) $action;
        return $this;
    }

    /**
     * Get the action name of the error handler.
     *  
     * @return string Name of the error action
     */
    public function getErrorAction()
    {
        return $this->_errorAction;
    }

    /**
     * Check for errors thrown by the router.
     * 
     * @param  AbstractRequest $request 
     * @return void
     */
    public function routeShutdown(AbstractRequest $request)
    {
        $this->_handleError($request);
    }

    /**
     * Check for errors thrown between routing and dispatching.
     * 
     * @param  AbstractRequest $request 
     * @return void
     */
    public function preDispatch(AbstractRequest $request)
    {
        $this->_handleError($request);
    }

    /**
     * Check for errors thrown by the dispatcher
     *
     * @param AbstractRequest $request
     * @return void 
     */
    public function postDispatch(AbstractRequest $request)
    {
        $this->_handleError($request);
    }

    /**
     * Handle any Exceptions thrown 
     *
     * @todo Log Errors
     * @todo  Different Error Codes: ROUTE_NOT_FOUND,INVALID_CONTROLLER usw
     * @param  AbstractRequest $request
     * @return void
     */
    protected function _handleError(AbstractRequest $request)
    {
        // Get the front controller instance
        $front = Front::getInstance();

        // Get the Response
        $response = $this->getResponse();

        // Check if the response has a registered exception 
        if ( $response->hasException() ) {

            $this->_handlerLoop = true;

            $error = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
            $trace = array();

            // get Exception information
            $exceptions = $response->getException();
            $exception = $exceptions[0];
            $exceptionType = get_Class($exception);
            $error->message = $exception->getMessage();
            $error->code = $exception->getCode();
            $error->file = $exception->getFile();
            $error->line = $exception->getLine();
   
            $error->trace = $exception->getTrace();

            switch($exceptionType)
            {
                case 'Nova\Controller\Dispatcher\Exception':
                    $error->type = self::DISPATCHER_EXCEPTION;
                    break;
                case 'Nova\Controller\Exception':
                    $error->type = self::CONTROLLER_EXCEPTION;
                    break;
                default:
                    $error->type = self::OTHER_EXCEPTION;
                    break;
            }

            // Store a copy of the original request
            $error->request = clone $request;

            // Forward to error handler
            $request->setParam('error-handler', $error)
                    ->setModuleName($this->getErrorModule())
                    ->setControllerName($this->getErrorController())
                    ->setActionName($this->getErrorAction())
                    ->setDispatched(false);

            // Remove the excpetion to prevent infinite loops
            $this->_response->removeFirstException();
        }
        
    
    }
}