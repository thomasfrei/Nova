<?php
/**
 * Nova - PHP Standard Router
 *
 * @package     Controller\Router
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace Nova\Controller\Router;

use Nova\Controller\Front as Front;
use Nova\Http\AbstractRequest as AbstractRequest;

/**
 * Standard Router
 *
 * Takes A Request And Assignes Module|Controller|Action
 *
 * @package     Controller\Router
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Class Standard implements RouterInterface
{
    /**
     * The Request to Route
     * @var AbstractRequest
     */
    protected $_request = null;

    /**
     * Holds the Parts of the Request Uri
     * @var array
     */
    protected $_parts = array();

    /**
     * Array Key to Use for Module
     * @var string
     */
    protected $_moduleKey;

    /**
     * Array Key to Use for Controller
     * @var string
     */
    protected $_controllerKey;

    /**
     * Array Key to Use for Action
     * @var string
     */
    protected $_actionKey;

    /**
     * Default Value for The Route
     * @var array
     */
    protected $_defaults = array();

    /**
     * Sets The Request.
     * 
     * @param AbstractRequest $request The Request to Route
     * @return Standard
     */
    public function setRequest(AbstractRequest $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Returns the Request.
     * 
     * @return AbstractRequest
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Get the Front Controller Instance.
     * 
     * @return Front
     */
    public function getFrontController()
    {
        return Front::getInstance();
    }

    /**
     * Route the Request.
     * 
     * @param  AbstractRequest $request Instance Of AbstractRequest
     * @return AbstractRequest $request
     */
    public function route(AbstractRequest $request)
    {
        // Set the Request
        $this->setRequest($request);

        // Set Defaults
        $this->setRequestKeys();

        // Get The Rewrite Base
        $base = $this->getRewriteBase($request);

        // index.php/
        if ($base === '/' || empty($base)) {
            // Empty Rewrite Base - Use Defaults
            $params = $this->_defaults;            
        } else {
            $base = trim($base, '/');
            $params = $this->match($base);
        }
        
        $request->setParams($params);
        return $request;
    }

    /**
     * Get the Rewrite Base
     * 
     * @param  AbstractRequest $request Instance of AbstractRequest
     * @return string $base Rewrite Base
     */
    public function getRewriteBase($request)
    {
        // Get The Request Uri
        $requestUri = $request->getRequestUri();

        // Get The Base Uri
        $baseUri = $request->getBaseUri();

        //Compare RequestUri with BaseUri
        $pos = strpos($baseUri, $requestUri);

        // with index.php in uri
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
     * Match the Request to Module, Controller an Action.
     * 
     * @param  string $path Rewrite Base
     * @return Array  An Array of Assigned Values
     */
    public function match($path)
    {
        $values = array();
        
        $parts = explode('/', $path);

        // Check for Module
        if ( count($parts) && (isset($parts[0])) ) {
            $values[$this->_moduleKey] = array_shift($parts);
        }

        // Check for Controller
        if ( count($parts) && (isset($parts[0])) ) {
            $values[$this->_controllerKey] = array_shift($parts);
        }

        // Check for action
        if ( count($parts) && (isset($parts[0])) ) {
            $values[$this->_actionKey] = array_shift($parts);
        }

        // Check for Params
        $params = array();
        for ($i=0; $i<sizeof($parts); $i=$i+2) {
            $params['actionParams'][$parts[$i]] = isset($parts[$i+1]) ? $parts[$i+1] : null;
        }

        $this->_parts = $values + $params;
        return $this->_parts + $this->_defaults;
    }

    /**
     * Set Request key and Default Values Based on the Request Object
     *
     * @return Standard
     */
    public function setRequestKeys()
    {
        $this->_moduleKey     = $this->_request->getModuleKey();
        $this->_controllerKey = $this->_request->getControllerKey();
        $this->_actionKey     = $this->_request->getActionKey();

        $dispatcher = $this->getFrontController()->getDispatcher();

        $this->_defaults = array(
            $this->_moduleKey     => $dispatcher->getDefaultModule(),
            $this->_controllerKey => $dispatcher->getDefaultController(),
            $this->_actionKey     => $dispatcher->getDefaultAction()
        );

        return $this;
    }

    /**
     * Returns The Defaults
     * @return Array Defaults
     */
    public function getDefaults()
    {
        return $this->_defaults;
    }
}