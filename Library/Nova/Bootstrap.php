<?php
/**
 * Nova - PHP 5 Framework
 *
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2012 Thomas Frei
 * @link        https://github.com/thomasfrei/nova
 * @license     https://github.com/thomasfrei/nova/blob/master/License.txt 
 * @package     Nova
 * @version     0.0.1 
 */

namespace Nova;

use Nova\Controller\Front as Front;

/**
 * Bootstrap
 *
 * @package Nova     
 */
Abstract Class Bootstrap
{
    /**
     * Tracing enabled flag
     * @var boolean
     */
    protected $tracing = false;

    /**
     * The Directory where traces are stored
     * @var string
     */
    protected $tracingDir = '/var/www/Nova/Logs/trace';

    /**
     * Profiling enabled flag
     * @var boolean
     */
    protected $profiling = false;

    /**
     * Array of init methods in the Application bootstrap
     * @var array
     */
    protected $initMethods = array();

    /**
     * Constructor
     * @param null|array $options Null or an array of options
     */
    public function __construct($options = null)
    {
        if ($options !== null) {
            $this->setOptions($options);
        }

        $this->getInitMethods();
    }

    /**
     * Set Bootstrap options
     * @param array $options Array of options
     * @return Bootstrap
     */
    public function setOptions($options)
    {
        if (array_key_exists('tracing', $options)) {
            if(array_key_exists('tracing.directory', $options)) {
                $this->setTracing($options['tracing'], $options['tracing.directory']);
            } else {
                $this->setTracing($options['tracing']);
            }
        }

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
     * Sets the tracing flag.
     * 
     * @param boolean $flag
     * @param string $directory Directory where traces are stored
     * @return Bootstrap
     */
    public function setTracing($flag, $directory = null)
    {
        if($directory !== null) {
            $this->tracingDir = $directory;
        }

        $this->tracing = $flag;
        return $this;
    }

    /**
     * Bootstrap the Application
     *
     * @throws Nova\Exception if tracing enabled but xdebug not installed
     */
    public function Bootstrap()
    {
        // if enabled start tracing
        if ($this->tracing){
            if (extension_loaded('xdebug')) {
                xdebug_start_trace($this->tracingDir);    
            } else {
                throw new Exception('Tracing not possible. Xdebug is Not installed');
            } 
        }

        // Set the error reposting 
        $this->setErrorReporting();

        // Instantiate Front Controller
        $frontController = Front::getInstance();

        // Set the module Directory
        $frontController->setModuleDirectory(APPPATH.'Modules'.DIRECTORY_SEPARATOR);

        // Run init methods from application bootstrap
        foreach ($this->initMethods as $method) {
            $this->$method();
        }

        // If profiling is enabled, register the profiler with the front controller
        if($this->profiling){
            $frontController->registerPlugin(new \Nova\Controller\Plugin\Profiler());
        }
        
        // Run application
        $frontController->dispatch();

        // End Tracing 
        if($this->tracing){
            xdebug_stop_trace();
        }
    }

    /**
     * Gets all the init prefixed methods in the application Bootstrap.
     * 
     * @return Bootstrap
     */
    public function getInitMethods()
    {
        $methods = get_class_methods($this);

        foreach($methods as $method){
           if (4 < strlen($method) && substr($method, 0, 4) === 'init') {
                    $this->initMethods[strtolower(substr($method, 4))] = $method;
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