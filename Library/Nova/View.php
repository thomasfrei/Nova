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
class View extends AbstractView
{
    /**
     * Include the view script
     * @return string
     */
	protected function _run()
    {
       return include func_get_arg(0);
    }
}