<?php
/**
 * Nova - PHP 5 Framework
 *
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2012 Thomas Frei
 * @link        https://github.com/thomasfrei/nova
 * @license 	https://github.com/thomasfrei/nova/blob/master/License.txt 
 * @package     Nova\View
 * @version     0.0.1 
 */

namespace Nova\View;

/**
 * Base View Class
 * 
 * @package 		Nova\View
 */
abstract class AbstractView{

	/**
	 * helper class Namespace
	 */
	protected $_helperNS = "Nova\View\Helper\\";

	/**
	 * Magic function loads helper class dynamically
	 * 
	 * @param  string $name
	 * @param  array $args
	 * @return Instance of Helper
	 */
	public function __call($name, $args)
	{
		$helper = $this->getHelper($name);
		
		// Call the helper
		return call_user_func_array(
			array($helper, $name), 
			$args
		);
	}

	/**
	 * Load a helper class
	 * 
	 * @param  string $name 
	 * @return object $helper
	 */
	public function getHelper($name)
	{
		$name = ucfirst(strtolower($name));
		$name = $this->_helperNS . $name;
		$helper = new $name();		
		return $helper;
	}

}

