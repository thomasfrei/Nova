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

use Nova\Controller\Dispatcher\Standard as Dispatcher;
use Nova\Http\Request;
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
Class StandardTest extends \PHPUnit_Framework_Testcase
{
	public function setup()
	{
		$this->dispatcher = new Dispatcher();
	}

	public function tearDown()
	{
		unset($this->dispatcher);
	}

	public function testClassToFilename()
	{
		$this->assertSame('IndexController.php', $this->dispatcher->classToFilename('IndexController'));
	}

	public function testIsValidModule()
	{
		$this->dispatcher->setModuleDirectory(TESTPATH.'NovaTests/Controller/Dispatcher/_files/Modules/');

        $this->assertTrue($this->dispatcher->isValidModule('Test'));
        $this->assertFalse($this->dispatcher->isValidModule('Foobar'));

	}

	public function testIsDispatchable()
    {
        $request = new Request();
        $request->setModuleName('Test')->setControllerName('Test');
        $this->dispatcher->setModuleDirectory(TESTPATH.'NovaTests/Controller/Dispatcher/_files/Modules/');

        $this->assertTrue($this->dispatcher->isDispatchable($request));

        $request->setControllerName('Trololo');
        $this->assertFalse($this->dispatcher->isDispatchable($request));
    }

    public function testThrowsExceptionWhenInvalidModule()
    {
    	$request = new Request();
    	$request->setModuleName('Invalid')
    			->setControllerName('foo')
    			->setActionName('bar');

    	$this->setExpectedException('Nova\Controller\Dispatcher\Exception', 'Requested Module "Invalid" Could Not Be Found');
    	$this->dispatcher->isDispatchable($request);
    }

    public function testDispatch()
    {
        $request = new Request('www.test.com/');
        $response = new Response();

        $this->setExpectedException('Nova\Controller\Dispatcher\Exception', 'Requested Module "" Could Not Be Found');
        $this->dispatcher->dispatch($request, $response);
    }
}