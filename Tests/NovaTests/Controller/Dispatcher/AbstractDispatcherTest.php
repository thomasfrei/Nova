<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     Controller\Dipatcher
 * @subpackage  NovaTests
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace NovaTests\Controller\Dispatcher;

use Nova\Controller\Dispatcher\AbstractDispatcher;
use Nova\Http\Response;

/**
 * AbstractDispatcher
 *
 * @package     Controller\Dipatcher
 * @subpackage  NovaTests
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Class AbstractDispatcherTest extends \PHPUnit_Framework_Testcase
{
	public function setup()
	{
		$this->dispatcher = $this->getMockForAbstractClass('Nova\Controller\Dispatcher\AbstractDispatcher');
	}

	public function tearDown()
	{}

	public function testSetAndGetResponse()
	{
		$response = new Response();
		$this->dispatcher->setResponse($response);

		$this->assertSame($response, $this->dispatcher->getResponse());
	}

	public function testSetAndGetDefaultModule()
	{
		$this->assertSame('home', $this->dispatcher->getDefaultModule());

		$this->dispatcher->setDefaultModule('foobar');
		$this->assertSame('foobar', $this->dispatcher->getDefaultModule());
	}

	public function testSetAndGetDefaultController()
	{
		$this->assertSame('index', $this->dispatcher->getDefaultController());

		$this->dispatcher->setDefaultController('foobar');
		$this->assertSame('foobar', $this->dispatcher->getDefaultController());
	}

	public function testSetAndGetDefaultAction()
	{
		$this->assertSame('index', $this->dispatcher->getDefaultAction());

		$this->dispatcher->setDefaultAction('foobar');
		$this->assertSame('foobar', $this->dispatcher->getDefaultAction());
	}

	public function testFormatModuleName()
	{
		$module = 'fOOBAR';
		$result = $this->dispatcher->formatModuleName($module);
		$this->assertSame('Foobar', $result);
	}

	public function testFormatControllerName()
	{
		$controller = 'fOoBAR';
		$result = $this->dispatcher->formatControllerName($controller);
		$this->assertSame('FoobarController', $result);
	}

	public function testFormatActionName()
	{
		$action = 'fOoBAR';
		$result = $this->dispatcher->formatActionName($action);
		$this->assertSame('foobarAction', $result);
	}

	public function testSetAndGetModuleDirectory()
	{
		$this->assertNull($this->dispatcher->getModuleDirectory());
		$this->dispatcher->setModuleDirectory('path/to/modules');
		$this->assertSame('path/to/modules', $this->dispatcher->getModuleDirectory());
	}

	public function testSetAndGetDirectoryController()
	{
		$this->assertSame('Controller', $this->dispatcher->getControllerDirectory());

		$this->dispatcher->setControllerDirectory('path/to/controller');
		$this->assertSame('path/to/controller', $this->dispatcher->getControllerDirectory());
	}

	public function testSetAndGetDispatchDirectory()
	{
		$this->dispatcher->setDispatchDirectory('dispatch/dir');
		$this->assertSame('dispatch/dir', $this->dispatcher->getDispatchDirectory());
	}
}