<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     Nova\Http
 * @subpackage  NovaTests
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace NovaTests\Http;

use Nova\Http\AbstractRequest as AbstractRequest;

/**
 * AbstractRequest Tests
 *
 * @package     Nova\Http
 * @subpackage  NovaTests
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Class AbstractRequestTest extends \PHPUnit_Framework_Testcase
{
    /**
     * @var AbstractRequest
     */
    protected $request;

    public function setup()
    {
        $this->request = $this->getMockForAbstractClass('Nova\Http\AbstractRequest');
    }

    public function tearDown()
    {
        unset($this->request);
    }

    public function testSetsAndGetsModuleKey()
    {
        $this->assertSame('module', $this->request->getModuleKey());
        $this->request->setModuleKey('mod');
        $this->assertSame('mod', $this->request->getModuleKey());
    }

    public function testSetsAndGetsControllerKey()
    {
        $this->assertSame('controller', $this->request->getControllerKey());
        $this->request->setControllerKey('class');
        $this->assertSame('class', $this->request->getControllerKey());
    }

    public function testSetsAndGetsActionKey()
    {
        $this->assertSame('action', $this->request->getActionKey());
        $this->request->setActionKey('method');
        $this->assertSame('method', $this->request->getActionKey());
    }

    public function testSetsAndGetsModuleName()
    {
        $this->request->setModuleName('foobar');
        $this->assertSame('foobar', $this->request->getModuleName());
        $this->request->setModuleName('example');
        $this->assertSame('example', $this->request->getModuleName());
    }

    public function testSetsAndGetsControllerName()
    {
        $this->request->setControllerName('foobar');
        $this->assertSame('foobar', $this->request->getControllerName());
        $this->request->setControllerName('example');
        $this->assertSame('example', $this->request->getControllerName());
    }

    public function testSetsAndGetsActionName()
    {
        $this->request->setActionName('foobar');
        $this->assertSame('foobar', $this->request->getActionName());
        $this->request->setActionName('example');
        $this->assertSame('example', $this->request->getActionName());
    }

    public function testSetsAndGetsSingleParam()
    {
        $this->request->setParam('foo', 'bar');
        $this->assertSame('bar', $this->request->getParam('foo'));
    }

    public function testGetParamReturnsDefaultValueIfKeyIsNotFound()
    {
        $this->request->setParam('foo', 'bar');
        $this->assertNotNull($this->request->getParam('foo'));
        $this->assertNull($this->request->getParam('baz'));
        $this->assertFalse($this->request->getParam('bar', false));
    }

    public function testClearAParam()
    {
        $this->request->setParam('foo', 'bar');
        $this->assertSame('bar', $this->request->getParam('foo'));

        $this->request->clearParam('foo');
        $this->assertNotSame('bar', $this->request->getParam('foo'));
    }

    public function testSetsAndGetsDispatchedFlag()
    {
        $this->assertTrue($this->request->isDispatched());

        $this->request->setDispatched(false);
        $this->assertFalse($this->request->isDispatched());
    }

    public function testSetMultipleParamsAtOnce()
    {
        $expected = array(
            'module'     => 'test',
            'controller' => 'test',
            'action'     => 'test'
        );

        $this->request->setParams($expected);
        $result = $this->request->getParams();

        $this->assertSame($expected, $result);
    }
}