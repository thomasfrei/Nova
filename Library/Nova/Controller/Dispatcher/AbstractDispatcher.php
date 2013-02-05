<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     Controller\Dispatcher
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace Nova\Controller\Dispatcher;

/**
 * Base Dispatcher Class
 *
 * @package     Controller\Dispatcher
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Abstract Class AbstractDispatcher
{
    /**
     * Default module
     * @var string
     */
    public $_defaultModule = 'home';

    /**
     * Default Controller
     * @var string
     */
    public $_defaultController = 'index';

    /**
     * Default action
     * @var string
     */
    public $_defaultAction = 'index';

    /**
     * Module Directory
     * @var string
     */
    protected $_moduleDirectory = null;

    /**
     * Controller Directory
     * @var string
     */
    protected $_controllerDirectory = null;

    /**
     * Set the Response
     * 
     * @param AbstractResponse $response 
     * @return AbstractDispatcher
     */
    public function setResponse($response)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Returns the Response
     * 
     * @return AbstractResponse
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Returns the Defaultmodule
     * 
     * @return string Default Module
     */
    public function getDefaultModule()
    {
        return $this->_defaultModule;
    }

    /**
     * Sets the Default Module
     * 
     * @param string $module Default Module
     * @return Standard
     */
    public function setDefaultModule($module)
    {
        $this->_defaultModule = (string) $module;
        return $this;
    }

    /**
     * Get the Default Controller
     * 
     * @return string Default Controller
     */
    public function getDefaultController()
    {
        return $this->_defaultController;
    }

    /**
     * Sets the Default Controller
     * 
     * @param string $controller Deafult Controller
     * @return Standard
     */
    public function setDefaultController($controller)
    {
        $this->_defaultController = (string) $controller;
        return $this;
    }

    /**
     * Gets the Default Action Method
     * 
     * @return string Default Action
     */
    public function getDefaultAction()
    {
        return $this->_defaultAction;
    }

    /**
     * Sets The Default Action
     * 
     * @param string $action Default Action
     * @return Standard
     */
    public function setDefaultAction($action)
    {
        $this->_defaultAction = (string) $action;
        return $this;
    }

    /**
     * Format the Module Name
     * 
     * @param  string $module Unformated Module Name
     * @return string $module Formated Module Name
     */
    public function formatModuleName($module)
    {
        $module = (string) ucfirst(strtolower($module));
        return $module;
    }

    /**
     * Format the Controller name
     * 
     * @param  string $controller Unformated Controller Name
     * @return string $controller Formated Controller name
     */
    public function formatControllerName($controller)
    {
        $controller = (string) ucfirst(strtolower($controller));
        return $controller . 'Controller';
    }

    /**
     * Format The Action Name
     * 
     * @param  string $action Unformated Action Name
     * @return string $action Formated Action Name
     */
    public function formatActionName($action)
    {
        $action = (string) strtolower($action);
        return $action . 'Action';
    }

    /**
     * Sets the Module Directory
     * 
     * @param string $moduleDir Path to Modules
     */
    public function setModuleDirectory($moduleDir)
    {
        $this->_moduleDirectory = (string) $moduleDir;
        return $this;
    }

    /**
     * Gets the Module Directory
     * 
     * @return string Module Directory
     */
    public function getModuleDirectory()
    {
        return $this->_moduleDirectory;
    }

    /**
     * Sets the Controller Directory
     * 
     * @param string $controllerDir Controller Directory
     */
    public function setControllerDirectory($controllerDir)
    {
        $this->_controllerDirectory = (string) $controllerDir;
        return $this;
    }

    /**
     * Gets the ControllerDirectory
     * 
     * @return string Controller Directory
     */
    public function getControllerDirectory()
    {
        if ($this->_controllerDirectory === null){
            $this->_controllerDirectory = 'Controller';
        }

        return $this->_controllerDirectory;
    }

    /**
     * Sets the Dispatch Directory
     * 
     * @param string $dispatchDir Dispatch Directory
     * @return AbstractDispatcher
     */
    public function setDispatchDirectory($dispatchDir)
    {
        $this->_dispatchDirectory = (string) $dispatchDir;
        return $this;
    }

    /**
     * Gets the Dispatch Directory
     * 
     * @return string Dispatch Directory
     */
    public function getDispatchDirectory()
    {
        return $this->_dispatchDirectory;
    }

}