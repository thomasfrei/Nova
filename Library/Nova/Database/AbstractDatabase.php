<?php
/**
 * Nova - PHP 5 Framework
 *
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2012 Thomas Frei
 * @link        https://github.com/thomasfrei/nova
 * @license 	https://github.com/thomasfrei/nova/blob/master/License.txt 
 * @package     Nova\Database
 * @version     0.0.1 
 */

namespace Nova\Database;

/**
 * Base Database Class
 * 
 * @package 		Nova\Database	
 */
abstract Class AbstractDatabase
{
	/**
	 * Database Connection
	 *
	 * @var Object|Ressource|Null
	 */
	protected $_connection = null;

	/**
	 * Connect to a database
	 * 
	 * @return [type] [description]
	 */
	abstract protected function _connect();

}