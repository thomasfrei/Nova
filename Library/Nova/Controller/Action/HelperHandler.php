<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     Controller\Action    
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace Nova\Controller\Action;

use Nova\Controller\Action\Helper\AbstractHelper as AbstractHelper;

/**
 * The Helper Handler handles the registering and retrieving 
 * of helpers
 *
 * @package     Controller\Action      
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Class HelperHandler
{
	/**
	 * Array of registered Helpers
	 * @var array
	 */
	protected static $_helpers = array();

	/**
	 * Action Controller
	 * @var \Nova\Controller\Action
	 */
	protected static $_actionController;

	/**
	 * Constructor
	 *
	 * @param \Nova\Controller\Action $actionController
	 * @return void
	 */
	public function __construct(\Nova\Controller\Action $actionController)
	{
		self::$_actionController = $actionController;
	}

	/**
	 * Register a Helper with
	 *
	 * Helpers are run in the order they are added.
	 *
	 * @param AbstractHelper $helper Instance of the Helper
	 * @return Handler
	 * @throws Nova\Controller\Action\Exception
	 */
	public function registerHelper($helper)
	{
		// Does the Helper extend AbstractHelper ?
		if (!$helper instanceof AbstractHelper) {
			throw new Exception("Helper must extend AbstractHelper");
		}

		// Is the Helper already Registered ?
		if ((array_search($helper, self::$_helpers, true)) !== false){
			return self;
		}

		$index = count(self::$_helpers);

		while(isset(self::$_helpers[$index])) {
			$index++;
		}

		self::$_helpers[$index] = $helper;

		// Sort just to be sure
		ksort(self::$_helpers);
	}

	/**
	 * Unregister a helper from the helper stack
	 *
	 * @param string | AbstractHelper $helper Instance of helper or classname
	 * @return HelperHandler;
	 */
	public function unregisterHelper($helper)
	{
		// Check if Instance
		if ($helper instanceof AbstractHelper) {
			$key = array_search($helper, self::$_helpers);
			if ($key === false) {
				// Helper was not registered
				return self;
			}
			unset(self::$_helpers[$key]);

		// or classname
		} elseif (is_string($helper)) {
			foreach (self::$_helpers as $key => $_helper) {
				$type = get_class($_helper);
				if ($helper === $type) {
					unset(self::$_helpers[$key]);
				}
			}
		}

		return self;	
	}

	/**
	 * Returns an array of all registered helpers
	 *
	 * @return array registered helpers
	 */
	public function getRegisteredHelpers()
	{
		return self::$_helpers;
	}

	/**
	 * Get a single helper by classname
	 *
	 * @param string $name Name of the helper class
	 * @return mixed Instance of helper class or false
	 */
	public static function getHelper($name)
	{
		foreach (self::$_helper as $key => $_helper) {
			$type = explode('\\', get_class($_helper));
			$type = end($type);

			if ($name == $type) {
				return self::$_helpers[$key];
			}
		}

		return false;
	}

	/**
     * Runs before the Action Controller has been Dispatched
     * 
     * @return void
     */
    public function preDispatch()
    {
    	foreach ($this->getRegisteredHelpers() as $helper) {
    		$helper->preDispatch();
    	}
    }

    /**
     * Runs after the Action Controller has been Dispatched
     * 
     * @return void
     */
    public function postDispatch()
    {
    	foreach ($this->getRegisteredHelpers() as $helper) {
    		$helper->postDispatch();
    	}
    }

    /**
     * Initialises Action Helpers
     * 
     * @return void
     */
    public function init()
    {
    	foreach ($this->getRegisteredHelpers() as $helper) {

    		$helper->setActionController(self::$_actionController);
    		$helper->init();
    	}
    }
}