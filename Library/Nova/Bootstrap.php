<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     Nova
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace Nova;

use Nova\Controller\Front as Front;

/**
 * Description
 *
 * @package     Nova
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Abstract Class Bootstrap
{
    /**
     * Profiling enabled flag
     * @var boolean
     */
    protected $profiling = false;

    /**
     * Array of init methods in the Application bootstrap
     * @var array
     */
    protected $_initMethods = array();

    /**
     * Constructor
     * @param null|array $options Null or an array of options
     */
    public function __construct($options = null)
    {
        if ($options !== null) {
            $this->setOptions($options);
        }
        
        $this->setInitMethods();
    }

    /**
     * Set Bootstrap options
     * @param array $options Array of options
     * @return Bootstrap
     */
    public function setOptions($options)
    {
        if (array_key_exists('profiling', $options)){
            $this->setProfiling($options['profiling']);
        }
        return $this;
    }

    /**
     * Sets the profiling flag.
     * 
     * @param boolean $flag
     * @return Bootstrap
     */
    public function setProfiling($flag)
    {
        $this->profiling = $flag;
        return $this;
    }

    /**
     * Is Profiling Enabled ?
     * 
     * @return boolean 
     */
    public function getProfiling()
    {
        return $this->profiling;
    }

    /**
     * Bootstrap the Application
     *
     * @throws Nova\Exception if tracing enabled but xdebug not installed
     */
    public function Bootstrap()
    {
        // Set the error reposting 
        $this->setErrorReporting();

        // Instantiate Front Controller
        $frontController = Front::getInstance();

        // Set the module Directory
        $frontController->setModuleDirectory(APPPATH.'Modules'.DIRECTORY_SEPARATOR);

        // Run init methods from application bootstrap
        foreach ($this->_initMethods as $method) {
            $this->$method();
        }

        // If profiling is enabled, register the profiler with the front controller
        if($this->profiling){
            $frontController->registerPlugin(new \Nova\Controller\Plugin\Profiler());
        }
        
        // Run application
        $frontController->dispatch();
    }

    /**
     * Gets all the init prefixed methods in the application Bootstrap.
     * 
     * @return Bootstrap
     */
    public function setInitMethods()
    {
        $methods = get_class_methods($this);

        foreach($methods as $method){
           if (4 < strlen($method) && substr($method, 0, 4) === 'init') {
                    $this->_initMethods[strtolower(substr($method, 4))] = $method;
                }
        }

        return $this;
    }

    /**
     * Set error reporting based on ENVIRONMENT constant
     *
     * @return void
     * @throws  Nova\Exception if invalid ENVIRONMENT constant
     */
    public function setErrorReporting()
    {
        switch(ENVIRONMENT)
        {
            case 'production' :
                error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
                ini_set('display_errors', 0);
                break;
            case 'testing':
            case 'development':
                error_reporting(-1);
                ini_set('display_errors', 1);
                break;
            default:
                throw new Exception('Invalid Environment setting');
        }
    }
}