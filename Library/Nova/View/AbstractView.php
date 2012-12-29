<?php 
/**
 * Nova - PHP 5 Framework
 *
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2012 Thomas Frei
 * @link        https://github.com/thomasfrei/nova
 * @license     https://github.com/thomasfrei/nova/blob/master/License.txt 
 * @package     Nova\View
 */

namespace Nova\View;

use Nova\View\Helper\AbstractHelper as AbstractHelper;

/**
 * Base View Object
 * 
 * @package         Nova\View
 */
abstract Class AbstractView
{
    /**
     * The view script to render
     * @var string
     */
    protected $_script;

    /**
     * The path to the view Script
     * @var string
     */
    protected $_scripPath;

    /**
     * Encoding to use in escaping
     * @var string
     */
    protected $_encoding = 'UTF-8';

    /**
     * Escaping
     * @var string
     */
    protected $_escape = 'htmlspecialchars';

    /**
     * Helper Namespace
     * @var string
     */
    protected $_helperNs = null;

    /**
     * Constructor
     *     
     * @param array $options Array of view options
     */
    public function __construct($options = array())
    {
    	// Set view options
    	if(!empty($options)){
    		$this->setOptions($options);	
    	}
    }

    /**
     * Set View options
     * @param array $options array of view options
     * @return AbstractView
     */
    public function setOptions($options = array())
    {
    	// find option setters
    	foreach($options as $key => $value) {
    		$method = 'set'.ucfirst(strtolower($key));
    		if(method_exists($this, $method)) {
    			$this->$method($value);
    		}
    	}
    	return $this;
    }

    /**
     * Directly assign a vlue in the view Script
     * @param string $key   The Variable name
     * @param mixed $value  The Variable value
     * @return void
     * @throws Nova\View\Exception if Trying to set a private or protected member
     */
    public function __set($key, $value)
    {
    	// prevent overwriting private or protected members
    	if(substr($key,0,1) === '_'){
    		throw new Exception('Setting private or protected class members is not allowed');
    	}

    	$this->$key = $value;
    	return;
    }

    /**
     * Prevent Error for nonexisting keys
     * @param string $key 
     * @return null
     */
    public function __get($key)
    {
        return null;
    }

    /**
     * Access a view helper
     * @param  string $name name of the view helper
     * @param  array $args The Parameters for the helper
     * @return string The helper output
     */
    public function __call($name, $args)
    {
    	$helper = $this->loadHelper($name);

    	return call_user_func_array(
            array($helper, $name),
            $args
        );
    }

    /**
     * Allows testing with empty() and isset() inside view scripts
     * @param  string  $key 
     * @return boolean
     */
    public function __isset($key)
    {
        if(substr($key,0,1) === '_') {
            return false;
        }

        return isset($this->$key);
    }

    /**
     * Unset a object property
     * @param string $key 
     * @return  void
     */
    public function __unset($key)
    {
        if( (substr($key,0,1) !== '_') && (isset($this->$key))){
            unset($this->$key);
        }
    }

    /**
     * Return all assigned view Variables
     * @return array Array of assigned variables
     */
    public function getVars()
    {
        $vars   = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if ('_' == substr($key, 0, 1)) {
                unset($vars[$key]);
            }
        }

        return $vars;
    }

    /**
     * Set the Script path
     * @param string $path
     * @return AbstractView
     */
    public function setScriptPath($path)
    {
        $this->_scripPath = $path;
        return $this;
    }

    /**
     * Returns the Script path
     * @return string Script path
     */
    public function getScriptPath()
    {
        return $this->_scripPath;
    }

    /**
     * Set the escaping mechanism
     * @see  http://ch2.php.net/manual/en/function.htmlentities.php
     * @see  http://ch2.php.net/manual/en/function.htmlspecialchars.php
     * @param string $spec The escaping mechanism to use
     * @return AbstractView
     */
    public function setEscape($spec)
    {
        $this->_escape = $spec;
        return $this;
    }

    /**
     * Returns the escaping mechanism
     * @return string
     */
    public function getEscape()
    {
        return $this->_escape;
    }

    /**
     * Set the encoding to use with htmlentities() and htmlspecialchars()
     * Defaults to 'UTF-8'.
     * 
     * @param string $spec 
     * @return AbstractView
     */
    public function setEncoding($spec)
    {
        $this->_encoding = $spec;
        return $this;
    }

    /**
     * Returns the encoding
     * @return [type] 
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * Load a helper by name
     * @param  string $name Name of the Helper
     * @return AbstractHelper
     */
    public function loadHelper($name)
    {
        $name = ucfirst($name);
        $helper = $this->getHelperNamespace().$name;

        return new $helper();
    }

    /**
     * Sets the helper Namespace
     * @param string $helperNamespace Namepace of helper classes
     * @return AbstractView
     */
    public function setHelperNamespace($helperNamespace)
    {
        $this->_helperNs = $helperNamespace;
        return $this;
    }

    /**
     * Returns the helper namespace
     * @return string
     */
    public function getHelperNamespace()
    {
        if($this->_helperNs === null) {
            $this->_helperNs = 'Nova\View\Helper\\';
        }
        return $this->_helperNs;
    }

    /**
     * Escpape a value for output in view script
     * @see  http://ch2.php.net/manual/en/function.htmlentities.php
     * @see  http://ch2.php.net/manual/en/function.htmlspecialchars.php
     * @param  string $var Value to escape
     * @return string escaped value
     */
    public function escape($var)
    {
        if (in_array($this->_escape, array('htmlentities', 'htmlspecialchars'))) {
            return call_user_func($this->_escape, $var, ENT_COMPAT, $this->_encoding);
        }
    }

    /**
     * Render the view script and return the output
     *         
     * @param  string $scriptName Name of the script to process
     * @return string The script output
     */
    public function render($scriptName)
    {
        $this->_script = $this->_findScript($scriptName);

        ob_start();
        $this->_run($this->_script);
        return ob_get_clean();
    }

    /**
     * Finds the View script to render
     * @param  string $scriptName Name of the Script
     * @return string Path to the script
     */
    protected function _findScript($scriptName)
    {
        $path = $this->_scripPath.DIRECTORY_SEPARATOR;

        if(is_dir($path)) {
            if(file_exists($path.$scriptName)){
                return $path.$scriptName;
            } else {
                throw new Exception('Script '. $scriptName.' not found in '. $path);
            }
        } else {
            throw new Exception('Directory: '. $path . ' not found');
        }
    }

    /**
     * Use to include the view script in a scope that only allows public
     * members.
     *
     * @return mixed
     */
    abstract protected function _run();
}