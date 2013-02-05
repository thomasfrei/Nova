<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     Controller
 * @subpackage  NovaTests
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace NavaTests\Controller;

use Nova\Controller\Front as Front;
use Nova\Http\Request as Request;
use Nova\Http\Response as Response;
use Nova\Controller\Router\Standard as Router;
use Nova\Controller\Dispatcher\Standard as Dispatcher;
use Nova\Controller\Plugins\AbstractPlugin;

/**
 * Front Controller Tests
 *
 * @package     Controller
 * @subpackage  NovaTests
 * @group       Front
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Class FrontTest extends \PHPUnit_Framework_Testcase
{
    public function setup()
    {
        $this->front = new Front();
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router();
        $this->dispatcher = new Dispatcher();

    }

    public function tearDown()
    {
        unset($this->front);
    }

    public function testSetRequestAcceptsRequestInstance()
    {
        $this->front->setRequest($this->request);
        $this->assertSame($this->request, $this->front->getRequest());
    }

    public function testSetRequestAcceptsClassNameString()
    {
        $this->front->setRequest('Nova\Http\Request');
        $result = $this->front->getRequest();
        $this->assertSame('Nova\Http\Request', get_class($result));
    }

    public function testReturnsDefaultRequestIfNoneIsSet()
    {
        $result = $this->front->getRequest();
        $this->assertTrue($result instanceof \Nova\Http\AbstractRequest);
        $this->assertSame('Nova\Http\Request', get_class($result));
    }

    public function testThrowsExceptionOnInvalidRequest()
    {
        $this->setExpectedException('Nova\Controller\Exception', 'Request Does Not Extend AbstractRequest');
        $this->front->setRequest('Nova\Controller\Router\Standard');
    }

    Public function testSetResponseAcceptsResponseInstance()
    {
        $this->front->setResponse($this->response);
        $this->assertSame($this->response, $this->front->getResponse());
    }

    public function testSetResponseAcceptsClassNameString()
    {
        $this->front->setResponse('Nova\Http\Response');
        $result = $this->front->getResponse();
        $this->assertSame('Nova\Http\Response', get_class($result));
    }

    public function testReturnsDefaultResponseIfNoneIsSet()
    {
        $result = $this->front->getResponse();
        $this->assertTrue($result instanceof \Nova\Http\AbstractResponse);
        $this->assertSame('Nova\Http\Response', get_class($result));
    }

    public function testThrowsExceptionOnInvalidResponse()
    {
        $this->setExpectedException('Nova\Controller\Exception','Response Does Not Extend AbstractResponse');
        $this->front->setResponse('Nova\Http\Request');
    }

    public function testSetRouterAcceptsRouterInstance()
    {
        $this->front->setRouter($this->router);
        $this->assertSame($this->router, $this->front->getRouter());
    }

    public function testSetRouterAcceptsClassNameString()
    {
        $this->front->setRouter('Nova\Controller\Router\Standard');
        $result = $this->front->getRouter();
        $this->assertSame('Nova\Controller\Router\Standard', get_class($result));
    }

    public function testReturnsStandardRouterIfNoneIsSet()
    {
        $result = $this->front->getRouter();
        $this->assertTrue($result instanceof \Nova\Controller\Router\RouterInterface);
        $this->assertSame('Nova\Controller\Router\Standard', get_class($result));
    }

    public function testThrowsExceptionOnInvalidRouter()
    {
        $this->setExpectedException('Nova\Controller\Exception', 'Router Does Not Implement RouterInterface');
        $this->front->setRouter('Nova\Http\Request');
    }

    public function testSetDispatcherAcceptsDispatcherInstance()
    {
        $this->front->setDispatcher($this->dispatcher);
        $this->assertSame($this->dispatcher, $this->front->getDispatcher());
    }

    public function testSetDispatcherAcceptsClassNameString()
    {
        $this->front->setDispatcher('Nova\Controller\Dispatcher\Standard');
        $result = $this->front->getDispatcher();
        $this->assertSame('Nova\Controller\Dispatcher\Standard', get_class($result));
    }

    public function testReturnsStandardDispatcherIfNoneIsSet()
    {
        $result = $this->front->getDispatcher();
        $this->assertTrue($result instanceof \Nova\Controller\Dispatcher\AbstractDispatcher);
        $this->assertSame('Nova\Controller\Dispatcher\Standard', get_class($result));
    }

    public function testThrowsExceptionOnINvalidDispatcher()
    {
        $this->setExpectedException('Nova\Controller\Exception', 'Dispatcher Does Not Extend AbstractDispatcher');
        $this->front->setDispatcher('Nova\Http\Request');
    }

    public function testSetsAndGetsModuleDirectory()
    {
        $this->front->setModuleDirectory('path/to/modules');
        $this->assertSame('path/to/modules', $this->front->getModuleDirectory());
    }

    public function testRegisteresPlugins()
    {
        $plugin = new TestPlugin();
        $this->front->registerPlugin($plugin);
        $result = $this->front->getRegisteredPlugins();
        $this->assertTrue(in_array($plugin, $result));
    }

    public function testUnregisteresPlugins()
    {
        $plugin = new TestPlugin();
        $this->front->registerPlugin($plugin);
        $result = $this->front->getRegisteredPlugins();
        $this->assertTrue(in_array($plugin, $result));

        $this->front->unregisterPlugin($plugin);
        $result = $this->front->getRegisteredPlugins();
        $this->assertFalse(in_array($plugin, $result));
    }

}

Class TestPlugin extends AbstractPlugin
{}