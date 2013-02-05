<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     Controller\Plugins    
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace NovaTests\Controller\Plugins;

use Nova\Controller\Plugins\ErrorHandler;
use Nova\Http\Request;
use Nova\Http\Response;

/**
 * ErrorHandler Tests
 *
 * @package     Controller\Plugins      
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Class ErrorHandlerTest extends \PHPUnit_Framework_Testcase
{
	public function setup()
	{
		$this->plugin = new ErrorHandler();
		$this->request = new Request();
		$this->response = new Response();

		$this->plugin->setRequest($this->request)
					 ->setResponse($this->response);
	}

	public function testSetErrorModule()
	{
		$this->assertSame('error', $this->plugin->getErrorModule());
		$this->plugin->setErrorModule('Exception');
		$this->assertSame('Exception', $this->plugin->getErrorModule());
	}

	public function testSetErrorController()
	{
		$this->assertSame('error', $this->plugin->getErrorController());
		$this->plugin->setErrorController('Exception');
		$this->assertSame('Exception', $this->plugin->getErrorController());
	}

	public function testSetErrorAction()
	{
		$this->assertSame('error', $this->plugin->getErrorAction());
		$this->plugin->setErrorAction('Exception');
		$this->assertSame('Exception', $this->plugin->getErrorAction());
	}

	public function testDispatcherException()
	{
		$exception = new \Nova\Controller\Dispatcher\Exception('Dispatcher Exception');
		$this->response->setException($exception);
		$this->request->setModuleName('Foo')
					  ->setControllerName('Bar')
					  ->setActionName('Baz');
		$this->plugin->postDispatch($this->request);

		$result = $this->request->getParam('error-handler');
		$this->assertTrue(ErrorHandler::DISPATCHER_EXCEPTION === $result->type);

		$this->assertSame('error', $this->request->getModuleName());
		$this->assertSame('error', $this->request->getControllerName());
		$this->assertSame('error', $this->request->getActionName());
	}

	public function testControllerException()
	{
		$exception = new \Nova\Controller\Exception('Controller Exception');
		$this->response->setException($exception);
		$this->request->setModuleName('Foo')
					  ->setControllerName('Bar')
					  ->setActionName('Baz');
		$this->plugin->preDispatch($this->request);

		$result = $this->request->getParam('error-handler');
		$this->assertTrue(ErrorHandler::CONTROLLER_EXCEPTION === $result->type);

		$this->assertSame('error', $this->request->getModuleName());
		$this->assertSame('error', $this->request->getControllerName());
		$this->assertSame('error', $this->request->getActionName());
	}

	public function testOtherException()
	{
		$exception = new \Nova\Loader\Exception('Other Exception');
		$this->response->setException($exception);
		$this->request->setModuleName('Foo')
					  ->setControllerName('Bar')
					  ->setActionName('Baz');
		$this->plugin->routeShutdown($this->request);

		$result = $this->request->getParam('error-handler');
		$this->assertTrue(ErrorHandler::OTHER_EXCEPTION === $result->type);

		$this->assertSame('error', $this->request->getModuleName());
		$this->assertSame('error', $this->request->getControllerName());
		$this->assertSame('error', $this->request->getActionName());
	}
}