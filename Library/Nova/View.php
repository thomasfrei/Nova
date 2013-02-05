<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     View
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

namespace Nova;

use Nova\View\AbstractView as AbstractView;

/**
 * Description
 *
 * @package     View
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
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