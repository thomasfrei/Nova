<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     Controller\Router
 * @subpackage  NovaTests
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace NovaTests\Controller\Router;

use Nova\Controller\Router\Standard as Router;
use Nova\Http\Request as Request;

/**
 * Description
 *
 * @package     Controller\Router
 * @subpackage  NovaTests
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Class StandardTest extends \PHPUnit_Framework_Testcase
{
    protected $router;

    protected $request;

    public function setup()
    {
        $this->router = new Router();
        $this->request = new Request();
    }

    public function tearDown()
    {
        unset($this->router, $this->request);
    }

    public function testRouterAcceptsRequestInstance()
    {
        $this->router->setRequest($this->request);
        $result = $this->router->getRequest();
        $this->assertSame($result, $this->request);
    }

    public function testGetsTheRewriteBase()
    {
        $this->request->setRequestUri('http://localhost/Nova/Public/Foo/Bar/Baz');
        $this->request->setBaseUri('http://localhost/Nova/Public/Index.php');
        $result = $this->router->getRewriteBase($this->request);

        $this->assertSame('/Foo/Bar/Baz', $result);

        $this->request->setRequestUri('http://www.example.com/index.php/mod/ctrl/act');
        $this->request->setBaseUri('http://www.example.com/index.php');
        $result = $this->router->getRewriteBase($this->request);

        $this->assertSame('/mod/ctrl/act', $result);
    }

    public function testSetsRequestKeys()
    {
        $method = new \ReflectionMethod($this->router, 'setRequestKeys');
        $method->setAccessible(TRUE);

        $expected = array(
            'module'     => 'home',
            'controller' => 'index',
            'action'     => 'index'
        );
        $this->router->setRequest($this->request)->setRequestKeys();
        $this->assertSame($expected,$this->router->getDefaults());
    }

    public function testRouterMatchesRequestToModuleControllerAction()
    {
        $this->router->setRequest($this->request)->setRequestKeys();
        $result = $this->router->match("Mod/Ctrl/Act");

        $this->assertTrue(is_array($result));

        $this->assertArrayHasKey('module', $result);
        $this->assertArrayHasKey('controller', $result);
        $this->assertArrayHasKey('action', $result);

        $this->assertSame($result['module'],'Mod');
        $this->assertSame($result['controller'],'Ctrl');
        $this->assertSame($result['action'],'Act');
    }

    public function testRouterGetsRequestParams()
    {
        $this->router->setRequest($this->request)->setRequestKeys();
        $result = $this->router->match("Mod/Ctrl/Act/key1/value1/key2/value2");

        $this->assertTrue(is_array($result));

        $this->assertArrayHasKey('actionParams', $result);
        $this->assertTrue(is_array($result['actionParams']));

        $this->assertArrayHasKey('key1', $result['actionParams']);
        $this->assertArrayHasKey('key2', $result['actionParams']);

        $this->assertSame($result['actionParams']['key1'],'value1');
        $this->assertSame($result['actionParams']['key2'],'value2');
    }

    public function testRouterRoutes()
    {
        $this->request->setRequestUri('http://www.example.com/index.php/Foo/Bar/Baz');
        $this->request->setBaseUri('http://www.example.com/index.php');

        $result = $this->router->route($this->request);

        $this->assertSame('Foo', $result->getModuleName());
        $this->assertSame('Bar', $result->getControllerName());
        $this->assertSame('Baz', $result->getActionName());
    }

    public function testRouterGetsDefaultValuesIfRewriteBaseIsEmpty()
    {
        $this->request->setRequestUri('http://www.example.com/');
        $this->request->setBaseUri('http://www.example.com/index.php');
        $result = $this->router->route($this->request);

        $expected = $this->router->getDefaults();
        $params = $result->getParams();

        $this->assertSame($expected, $params);
    }
}
