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

use Nova\Bootstrap;

/**
 * Bootstrap Test
 *
 * @package     Nova
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Class BootstrapTest extends \PHPUnit_Framework_Testcase
{
	public function setup()
	{
		$this->boot = $this->getMockForAbstractClass('Nova\Bootstrap');
	}

	public function tearDown()
	{
		unset($this->boot);
	}

	public function testSetAndGetProfiling()
	{
		$this->assertFalse($this->boot->getProfiling());

		$this->boot->setProfiling(true);
		$this->assertTrue($this->boot->getProfiling());
	}

	public function testSetOptionsInConstructor()
	{
		unset($this->boot);
		$options = array(
			'profiling'	=> true,
		);
		$this->boot = new TestBootstrap($options);

		$this->assertTrue($this->boot->getProfiling());
	}
}

class TestBootstrap extends Bootstrap
{}