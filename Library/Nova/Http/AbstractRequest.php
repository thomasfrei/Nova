<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     Http
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace Nova\Http;

/**
 * AbstractRequest
 *
 * @package     Http
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Abstract Class AbstractRequest
{
    /**
     * The Module Key For Request Params
     * @var string
     */
    protected $_moduleKey = 'module';

    /**
     * The Controller Key For Request Params
     * @var string
     */
    protected $_controllerKey = 'controller';

    /**
     * The Action Key For Request Params
     * @var string
     */
    protected $_actionKey = 'action';

    /**
     * Request Params
     * @var array
     */
    protected $_params = array();

    /**
     * Dispatch Flag
     * @var boolean
     */
    protected $_dispatched = true;

    /**
     * Returns the Module Key.
     * 
     * @return string Module Key
     */
    public function getModuleKey()
    {
        return $this->_moduleKey;
    }

    /**
     * Sets a New Module Key.
     * 
     * @param string $moduleKey Module Key
     * @return AbstractRequest
     */
    public function setModuleKey($moduleKey)
    {
        $this->_moduleKey = (string) $moduleKey;
        return $this;
    }

    /**
     * Returns the Controller Key.
     * 
     * @return string Controller Key
     */
    public function getControllerKey()
    {
        return $this->_controllerKey;
    }

    /**
     * Sets the Controller Key.
     * 
     * @param string $controllerKey Controller Key
     * @return AbstractRequest
     */
    public function setControllerKey($controllerKey)
    {
        $this->_controllerKey = (string) $controllerKey;
        return $this;
    }

    /**
     * Returns the Action Key.
     * 
     * @return string Action Key
     */
    public function getActionKey()
    {
        return $this->_actionKey;
    }

    /**
     * Sets the Action Key.
     * 
     * @param string $actionKey Action Key
     * @return AbstractRequest
     */
    public function setActionKey($actionKey)
    {
        $this->_actionKey = (string) $actionKey;
        return $this;
    }

    /**
     * Returns the Module Name.
     * 
     * @return string Module Name
     */
    public function getModuleName()
    {
        return $this->getParam($this->getModuleKey());
    }

    /**
     * Sets the Module Name.
     * 
     * @param string $moduleName Module Name
     * @return AbstractRequest
     */
    public function setModuleName($moduleName)
    {
        $moduleName = (string) $moduleName;
        $this->setParam($this->getModuleKey(), $moduleName);

        return $this;
    }

    /**
     * Returns the Controller Name.
     * 
     * @return string Controller Name
     */
    public function getControllerName()
    {
        return $this->getParam($this->getControllerKey());
    }

    /**
     * Sets the Controller Name.
     * 
     * @param string $controllerName Controller Name
     * @return AbstractRequest
     */
    public function setControllerName($controllerName)
    {
        $controllerName = (string) $controllerName;
        $this->setParam($this->getControllerKey(), $controllerName);

        return $this;
    }

    /**
     * Returns the Action Name.
     * 
     * @return string Action name
     */
    public function getActionName()
    {
        return $this->getParam($this->getActionKey());
    }

    /**
     * Set the Action Name
     * @param string $actionName Action name
     * @return AbstractRequest
     */
    public function setActionName($actionName)
    {
        $actionName = (string) $actionName;
        $this->setParam($this->getActionKey(), $actionName);

        return $this;
    }

    /**
     * Returns an Action Param.
     * 
     * Returns the Default Value if The Key is Not Found.
     * 
     * @param  string $key     Array key
     * @param  mixed  $default Default Value to Return if Key is not Found
     * @return mixed
     */
    public function getParam($key, $default = null)
    {
        $key = (string) $key;

        if (isset($this->_params[$key])) {
            return $this->_params[$key];
        }

        return $default;
    }

    /**
     * Sets an Action Param.
     * 
     * @param string $key   Array Key
     * @param mixed  $value
     * @return AbstractRequest
     */
    public function setParam($key, $value)
    {
        $key = (string) $key;
        $this->_params[$key] = $value;

        return $this;
    }

    /**
     * Clears an Action Param
     * @param  string $key Array Key
     * @return AbstractRequest
     */
    public function clearParam($key)
    {
        if (isset($this->_params[$key])) {
            unset($this->_params[$key]);
        }

        return $this;
    }

    /**
     * Set Multiple Params At Once
     * @param Array $params Array of Params
     * @return AbstractRequest
     */
    public function setParams($params)
    {
        foreach($params as $key => $value) {
            $this->_params[$key] = $value;
        }
        return $this;
    }

    /**
     * Returns Array Of All Params
     * @return Array Array of Params
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Checks if the Request has been Dispatched.
     * 
     * @return boolean
     */
    public function isDispatched()
    {
        return $this->_dispatched;
    }

    /**
     * Set Flag Indicating the Dispatch Status.
     *     
     * @param boolean $flag
     * @return AbstractRequest
     */
    public function setDispatched($flag = true)
    {
        $this->_dispatched = $flag ? true : false ;
        return $this;
    }
}