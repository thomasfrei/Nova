<?php
/**
 * Nova - PHP 5 Framework
 *
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2012 Thomas Frei
 * @link        https://github.com/thomasfrei/nova
 * @license 	https://github.com/thomasfrei/nova/blob/master/License.txt 
 * @package     Nova
 * @version     0.0.1 
 */

namespace Nova;

use Nova\View\AbstractView as AbstractView;

/**
 * View Class
 * 
 * @package Nova
 */
class View extends AbstractView{

	/**
	 * Path to the view script
	 * @var string
	 */
	protected $_script = null;

	/**
	 * Constructor
	 *
	 * @param string $script
	 */
	public function __construct($script)
	{
		$this->_script = $script;
	}

	/**
	 * Magic function to allow setting view variables
	 * @param string $key
	 * @param string $value
	 */
	public function __set($key, $value)
	{
		$this->$key = $value;
	}

	/**
	 * Render the view
	 *
	 * @return string
	 */
	public function render()
	{
		ob_start();
		require $this->_script;
		return ob_get_clean();
	}

}