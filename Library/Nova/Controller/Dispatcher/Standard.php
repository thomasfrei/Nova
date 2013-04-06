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

use Nova\Http\AbstractRequest;
use Nova\Http\AbstractResponse;
use Nova\Controller\Dispatcher\AbstractDispatcher;

/**
 * Standard Dispatcher
 *
 * @package     Controller\Dispatcher
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Class Standard extends AbstractDispatcher
{
    /**
     * Transforms Classnames to Filenames
     * 
     * @param  string $classname Name of the Class
     * @return string Name of the File
     */
    public function classToFilename($classname)
    {
        return $classname . '.php';
    }

    /**
     * Checks if the module exists in the  module directory
     *  
     * @param  string  $module Name of the module
     * @return boolean         
     */
    public function isValidModule($module)
    {
        $moduleDir = $this->getModuleDirectory();
        return (is_dir($moduleDir.$module)) ? true : false;
    }

    /**
     * Checks if Controller exists in the module Directory
     * 
     * @param AbstractRequest
     * @return boolean
     */
    public function isDispatchable(AbstractRequest $request)
    {
        // get the module directory an the module
        $moduleDir = $this->getModuleDirectory();
        $module = $this->formatModuleName($request->getModuleName());

        if(!$this->isValidModule($module)){
            throw new Exception('Requested Module "'.$module. '" Could Not Be Found');
        }

        // get the controller directory and the controller
        $controllerDir = $this->getControllerDirectory();
        $controller = $this->formatControllerName($request->getControllerName());

        $pathToController = $moduleDir.$module.DIRECTORY_SEPARATOR.$controllerDir;

        // construct the filename
        $fileName = $this->classToFilename($controller);

        // final file
        $finalFile = $pathToController.DIRECTORY_SEPARATOR.$fileName;

        if(!is_dir($pathToController) || !file_exists($finalFile)) {
            return false;
        }

        $this->setDispatchDirectory($pathToController.DIRECTORY_SEPARATOR);
        return true;
    }

    /**
     * Dispatch the request 
     * 
     * @param  AbstractRequest  $request Request Object
     * @param  AbstractResponse $response Response Object
     * @throws Nova\Controller\Dispatcher\Exception
     * @return Void
     */
    public function dispatch(AbstractRequest $request, AbstractResponse $response)
    {
        $this->setResponse($response);

        if(!$this->isDispatchable($request)){
            $request->setDispatched(false);
            $controller = $this->formatControllerName($request->getControllerName());
            throw new Exception('Controller File: '.$controller.' not found');
            
        }

        $controller = $this->formatControllerName($request->getControllerName());

        $this->loadController($controller);
        
        if(!class_exists($controller,false)){
            throw new Exception('Class: '.$controller.' not found');            
        }

        $controllerClass = new $controller($request, $this->getResponse(), $request->getParam('actionParams'));

        if(!$controllerClass instanceof \Nova\Controller\Action){
            throw new Exception("Controller must extend Nova\Controller\Action");
        }
        
        // Extract the requested action
        $action = $this->formatActionName($request->getActionName());

        // check if the method exists
        $actionMethods = get_class_methods($controllerClass);

        // Method does not exist
        if(!in_array($action, $actionMethods)){
            $action = substr($action, 0 , strlen($action) - 6);
            throw new Exception($action.'Action does not exist in '.$controller);
        }

        $request->setDispatched(true);

        // Call action
        ob_start();

        try {
            $controllerClass->dispatch($action);
        } catch (\Exception $e) {
            throw $e;
        }

        $content = ob_get_clean();
        $this->_response->appendBody($content);
        
        $controllerClass = null;
    }

    /**
     * Load the Requested controller
     * @param  string $className Name of the controller class
     * @return void
     */
    public function loadController($className)
    {
        // Get the dispatch directory
        $dispatchDir = $this->getDispatchDirectory();
        $fileName = $this->classToFilename($className);

        require_once($dispatchDir.$fileName);
    }
}