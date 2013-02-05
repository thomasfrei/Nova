<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     Controller    
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace NovaTests\Controller;

use Nova\Controller\PluginHandler as PluginHandler;
use Nova\Controller\Plugins\AbstractPlugin;

use Nova\Http\AbstractRequest;
use Nova\Http\Request;

use Nova\Http\Response;
use Nova\Controller\Front;

/**
 * PLuginHandlerTest
 *
 * @package     Controller      
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Class PluginHandlerTest extends \PHPUnit_Framework_Testcase
{
	public function setup()
    {
        // Use Http request as a proxy
        $this->plugin = new PluginHandler();
    }

    public function tearDown()
    {
        unset($this->plugin);
    }

	public function testCantRegisterPluginWithInvalidIndex()
    {
        $this->setExpectedException('Nova\Controller\Exception', 'Index must be set to a value between -100 and 100');
        $this->plugin->registerPlugin('stub',1000);
    }

    public function testCantRegisterPluginWithInvalidIndex1()
    {
        $this->setExpectedException('Nova\Controller\Exception', 'Index must be set to a value between -100 and 100');
        $this->plugin->registerPlugin('stub',-400);
    }

    public function testPluginMustExtendAbstractPlugin()
    {
        $plugin = new TestPlugin2();

        $this->setExpectedException('Nova\Controller\Exception', 'Plugin must extend AbstractPlugin');
        $this->plugin->registerPlugin($plugin, 20);
        unset($plugin);
    }

    public function testThrowsExceptionOnRegisteringPluginTwice()
    {
        $plugin = new TestPLugin();

        $this->setExpectedException('Nova\Controller\Exception', 'Plugin already registered');
        $this->plugin->registerPlugin($plugin, 10);
        $this->plugin->registerPlugin($plugin, 20);
        unset($plugin);
    }

    public function testThrowsExceptionOnReservedIndex()
    {
        $plugin1 = new TestPlugin();
        $plugin2 = new TestPlugin3();

        $this->setExpectedException('Nova\Controller\Exception','Plugin with index 20 already registered');
        $this->plugin->registerPlugin($plugin1, 20);
        $this->plugin->registerPlugin($plugin2, 20);
        unset($plugin1, $plugin2);
    }

    public function testSetsIndexAutomaticallyOnNull()
    {
        $plugin = new TestPLugin();
        $plugin3 = new TestPlugin3();

        $this->plugin->registerPlugin($plugin,1);
        $this->plugin->registerPlugin($plugin3);

        $expected = array('1' => $plugin, '2'  => $plugin3);
        $result = $this->plugin->getRegisteredPlugins();

        $this->assertSame($expected, $result);
        unset($plugin, $plugin3);
    }

    public function testUnregisteringPluginByInstance()
    {
        $plugin = new TestPLugin();
        $plugin3 = new TestPlugin3();

        $this->plugin->registerPlugin($plugin,1);
        $this->plugin->registerPlugin($plugin3,20);

        $expected = array('1' => $plugin, '20'  => $plugin3);
        $this->assertSame($expected, $this->plugin->getRegisteredPlugins());

        $expected = array('20' => $plugin3);
        $this->plugin->unregisterPlugin($plugin);
        $this->assertSame($expected, $this->plugin->getRegisteredPlugins());
        unset($plugin, $plugin3);
    }

    public function testUnregiteringPluginByPluginName()
    {
        $plugin = new TestPLugin();
        $plugin3 = new TestPlugin3();

        $this->plugin->registerPlugin($plugin,1);
        $this->plugin->registerPlugin($plugin3,20);

        $expected = array('1' => $plugin, '20'  => $plugin3);
        $this->assertSame($expected, $this->plugin->getRegisteredPlugins());

        $expected = array('20' => $plugin3);
        $this->plugin->unregisterPlugin('NovaTests\Controller\TestPlugin');
        $this->assertSame($expected, $this->plugin->getRegisteredPlugins());
        unset($plugin, $plugin3);
    }

    public function testThrowsExceptionWhenUnregisteringUnregisteredPlugin()
    {
        $this->setExpectedException('Nova\Controller\Exception','Plugin never registered');

        $plugin = new TestPLugin();
        $this->plugin->unregisterPlugin($plugin);
        unset($plugin);
    }

    public function testSetGetRequest()
    {
        $request = new Request();

        $this->plugin->setRequest($request);
        $this->assertSame($request, $this->plugin->getRequest());
        unset($request);
    }

    public function testSetGetRequestWithPlugins()
    {
        $request = new Request();
        $plugin = new TestPlugin();

        $this->plugin->registerPLugin($plugin);
        $this->plugin->setRequest($request);

        $this->assertSame($request, $plugin->getRequest());
        unset($request, $plugin);
    }

    public function testSetGetResponse()
    {
        $response = new Response();

        $this->plugin->setResponse($response);
        $this->assertSame($response, $this->plugin->getResponse());
        unset($response);
    }

    public function testSetGetResponseWithPlugins()
    {
        $response = new Response();
        $plugin = new TestPlugin();

        $this->plugin->registerPLugin($plugin);
        $this->plugin->setResponse($response);

        $this->assertSame($response, $plugin->getResponse());
        unset($plugin, $response);
    }

    public function testHandlerCatchesPluginExceptions()
    {
        $request = new Request();
        $response = new Response();
        $handler = new PluginHandler();
        $handler->setRequest($request);
        $handler->setResponse($response);
        $handler->registerPlugin(new ExceptionPlugin());

        try {
            $handler->routeStartup($request);
            $handler->routeShutdown($request);
            $handler->preDispatch($request);
            $handler->postDispatch($request);
            $handler->dispatchLoopStartup($request);
            $handler->dispatchLoopShutdown($request);
           
        } catch (Exception $e) {
           
        }

        $exceptions = $handler->getResponse()->getException();

        foreach($exceptions as $exception) {
            $result[] = $exception->getMessage();
        }
       
       $this->assertContains('routeStartup Exception thrown', $result);
       $this->assertContains('routeShutdown Exception thrown', $result);
       $this->assertContains('preDispatch Exception thrown', $result);
       $this->assertContains('postDispatch Exception thrown', $result);
       $this->assertContains('dispatchLoopStartup Exception thrown', $result);
       $this->assertContains('dispatchLoopShutdown Exception thrown', $result);
    }

    public function testRouteStartUpThrowsException()
    {
        $request = new Request();
        $handler = new PluginHandler();
        $handler->setRequest($request);
        $handler->registerPlugin(new ExceptionPlugin());
        Front::getInstance()->throwExceptions(true);

        $this->setExpectedException('\Nova\Controller\Exception', 'routeStartup Exception thrown');
        $handler->routeStartup($request);
        unset($request, $handler);
    }

    public function testRouteShutdownThrowsException()
    {
        $request = new Request();
        $handler = new PluginHandler();
        $handler->setRequest($request);
        $handler->registerPlugin(new ExceptionPlugin());
        Front::getInstance()->throwExceptions(true);

        $this->setExpectedException('\Nova\Controller\Exception', 'routeShutdown Exception thrown');
        $handler->routeShutdown($request);
        unset($request, $handler);
    }

    public function testPreDispatchThrowsException()
    {
        $request = new Request();
        $handler = new PluginHandler();
        $handler->setRequest($request);
        $handler->registerPlugin(new ExceptionPlugin());
        Front::getInstance()->throwExceptions(true);

        $this->setExpectedException('\Nova\Controller\Exception', 'preDispatch Exception thrown');
        $handler->preDispatch($request);
        unset($request, $handler);
    }

    public function testPostDispatchThrowsException()
    {
        $request = new Request();
        $handler = new PluginHandler();
        $handler->setRequest($request);
        $handler->registerPlugin(new ExceptionPlugin());
        Front::getInstance()->throwExceptions(true);

        $this->setExpectedException('\Nova\Controller\Exception', 'postDispatch Exception thrown');
        $handler->postDispatch($request);
        unset($request, $handler);
    }

    public function testDispatchLoopStartupThrowsException()
    {
        $request = new Request();
        $handler = new PluginHandler();
        $handler->setRequest($request);
        $handler->registerPlugin(new ExceptionPlugin());
        Front::getInstance()->throwExceptions(true);

        $this->setExpectedException('\Nova\Controller\Exception', 'dispatchLoopStartup Exception thrown');
        $handler->dispatchLoopStartup($request);
        unset($request, $handler);
    }

    public function testDispatchLoopShutdownThrowsException()
    {
        $request = new Request();
        $handler = new PluginHandler();
        $handler->setRequest($request);
        $handler->registerPlugin(new ExceptionPlugin());
        Front::getInstance()->throwExceptions(true);

        $this->setExpectedException('\Nova\Controller\Exception', 'dispatchLoopShutdown Exception thrown');
        $handler->dispatchLoopShutdown($request);
        unset($request, $handler);
    }


}

class TestPlugin extends AbstractPlugin
{

}

class TestPlugin2
{

}

class TestPlugin3 extends AbstractPlugin
{

}

class ExceptionPlugin extends AbstractPlugin
{

    public function routeStartup(AbstractRequest $request)
    {
        throw new \Nova\Controller\Exception('routeStartup Exception thrown');
    }

    public function routeShutdown(AbstractRequest $request)
    {
        throw new \Nova\Controller\Exception('routeShutdown Exception thrown');
    }

    public function preDispatch(AbstractRequest $request)
    {
        throw new \Nova\Controller\Exception('preDispatch Exception thrown');
    }

    public function postDispatch(AbstractRequest $request)
    {
        throw new \Nova\Controller\Exception('postDispatch Exception thrown');
    }

    public function dispatchLoopStartup(AbstractRequest $request)
    {
        throw new \Nova\Controller\Exception('dispatchLoopStartup Exception thrown');
    }

    public function dispatchLoopShutdown(AbstractRequest $request)
    {
        throw new \Nova\Controller\Exception('dispatchLoopShutdown Exception thrown');
 
    }
}