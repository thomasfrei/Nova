<?php 
/**
 * Nova - PHP 5 Framework
 *
 * @package     View\Helper
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace NovaTests\View;

use Nova\View\Helper\Doctype;

/**
 * Description
 *
 * @package     View\Helper
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Class DoctypeTest extends \PHPUnit_Framework_Testcase
{
	public function setup()
	{
		$this->doctype = new Doctype();
	}

	public function tearDown()
	{}

	public function testDefaultDoctype()
	{
		$result = $this->doctype->doctype();
		$this->assertStringStartsWith('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"',$result);
	}

	public function testSetAndGetDoctype()
	{
		$result = $this->doctype->doctype('HTML5');
		$this->assertSame('<!DOCTYPE html>', $result);

		$result = $this->doctype->getDoctype();
		$this->assertSame('<!DOCTYPE html>', $result);

		$result = $this->doctype->doctype();
		$this->assertSame('<!DOCTYPE html>', $result);

		$result = $this->doctype->__toString();
		$this->assertSame('<!DOCTYPE html>', $result);


	}
}