<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     Nova
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace NovaTests;

use Nova\Exception as Exception;

/**
 * Exception Test
 *
 * @package     Nova
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Class ExceptionTests extends \PHPUnit_Framework_Testcase
{
    public function setup()
    {
        $this->e = new Exception('Test Exception', 123);
    }

    public function testException()
    {
        $this->assertTrue($this->e instanceof \Exception);
        $this->assertSame('Test Exception', $this->e->getMessage());
    }
}
