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

use Nova\View as View;
use Nova\Http\AbstractRequest as AbstractRequest;
use Nova\Http\AbstractResponse as AbstractResponse;
use Nova\Controller\Action\HelperHandler as HelperHandler;

/**
 * Base Class for Action Controllers
 *
 * @package     Controller
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
abstract class Action{

    /**
     * The Request Object
     * @var AbstractRequest
     */
    protected $_request = null;

    /**
     * The Response Object
     * @var AbstractResponse
     */
    protected $_response = null;

    /**
     * Array of Action params
     * @var array
     */
    protected $_actionParams = array();

    /**
     * Helper Handler
     * @var HelperHandler
     */
    protected $_helper = null;
    /**
     * The view Object
     * @var View
     */
    public $view;

    /**
     * Contructor
     * 
     * @param AbstractRequest  $request 
     * @param AbstractResponse $response
     * @param array $actionParams 
     */
    public function __construct(AbstractRequest $request, AbstractResponse $response, array $actionParams = null)
    {
        $this->setRequest($request)
             ->setResponse($response)
             ->setActionParams($actionParams);
        $this->_helper = new HelperHandler($this);
        $this->init();
    }

    /**
     * Initialize Action Controller
     * 
     * @return void
     */
    public function init()
    {}

    /**
     * Sets the Request
     * 
     * @param AbstractRequest $request 
     */
    public function setRequest(AbstractRequest $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Gets the Request
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
     * @param AbstractResponse $response
     * @return Action
     */
    public function setResponse(AbstractResponse $response)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Gets the Response
     * 
     * @return AbstractResponse
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Gets the Action Params
     * 
     * @return Array
     */
    public function getActionParams()
    {
        return $this->_actionParams;
    }

    /**
     * Sets the Action Params
     * 
     * @param array $actionParams Array of Action Params
     * @return Action
     */
    protected function setActionParams($actionParams)
    {
        $this->_actionParams = $actionParams;
        return $this;
    }

    /**
     * Dispatch the action method
     * 
     * @param string $action Name of the action method
     * @return void
     */
    public function dispatch($action)
    {
        // Register ViewRenderer
        $this->_helper->registerHelper(new \Nova\Controller\Action\Helper\ViewRenderer());

        // Initialise helpers
        $this->_helper->init();

        // pre Dispatch
        $this->_helper->preDispatch();

        // Run Action Method
        $this->$action();
   
        // post Dispatch
        $this->_helper->postDispatch();
    }
}