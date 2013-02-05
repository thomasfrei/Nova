<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     Nova
 * @subpackage  Tests
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */


// --------------------------------------------------------------
// Define The Root Path
// --------------------------------------------------------------
defined('NOVAROOT') or define('NOVAROOT', realpath(dirname(__FILE__).'/../') .'/');
defined("TESTPATH") or define("TESTPATH", NOVAROOT . 'Tests/');


// --------------------------------------------------------------
// Define and Set The Include Path 
// --------------------------------------------------------------
$includePath = array(
    NOVAROOT.'Library',
    get_include_path()
);

set_include_path(implode(PATH_SEPARATOR, $includePath));

// --------------------------------------------------------------
// Register The Autoloader
// --------------------------------------------------------------
spl_autoload_register('loadFile');

// --------------------------------------------------------------
// Simple Test Suite Autoloader
// --------------------------------------------------------------
function loadFile($class)
{
    if (class_exists($class, false)){
        return true;
    }
        
    $class = str_replace('\\', '/', $class).'.php';
    if(stream_resolve_include_path($class)){
        include $class;
    }
}