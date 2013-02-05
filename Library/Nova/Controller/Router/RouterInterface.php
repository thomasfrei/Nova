<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     Controller\Router
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace Nova\Controller\Router;

use Nova\Http\AbstractRequest as AbstractRequest;

/**
 * Router Interface
 *
 * @package     Controller\Router
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Interface RouterInterface
{
    /**
     * Route the Request
     * 
     * @param  AbstractRequest $request Instance of AbstractRequest
     * @return AbstractRequest $request
     */
    public function route(AbstractRequest $request);
}