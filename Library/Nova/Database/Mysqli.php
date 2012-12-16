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
 * Mysqli Adapter 
 * 
 * @package 		Nova\Database
 * @subpackage 	
 */
Class Mysqli extends AbstractDatabase
{
	/**
	 * Connect to a Database
	 * 
	 * @return [type] [description]
	 */
	protected function _connect()
	{
		if($this->_connection){
			return;
		}
	}

}