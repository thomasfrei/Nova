<?php

namespace Nova\Controller\Dispatcher\Unittests;

use Nova\Controller\Dispatcher\Standard as Standard;
use Nova\Controller\Request\Http as Request;
use Nova\Controller\Response\Http as Response;

require_once BASEPATH.'Nova/Controller/Exception.php';
include BASEPATH.'Nova/Controller/Dispatcher/AbstractDispatcher.php';
include BASEPATH.'Nova/Controller/Dispatcher/Standard.php';
require_once BASEPATH.'Nova/Controller/Dispatcher/Exception.php';
require_once BASEPATH.'Nova/Controller/Action.php';


Class Standardtest extends \PHPUnit_Framework_Testcase
{
    public function setup()
    {
        $this->dispatcher = new Standard();
    }

    public function tearDown()
    {
        unset($this->dispatcher);
    }

    public function testDefaultValues()
    {
        $expectedModule     = 'default';
        $expectedController = 'index';
        $expectedAction     = 'index';

        $module = $this->dispatcher->getDefaultModule();
        $controller = $this->dispatcher->getDefaultController();
        $action = $this->dispatcher->getDefaultAction();

        $this->assertSame($expectedModule, $module);
        $this->assertSame($expectedController, $controller);
        $this->assertSame($expectedAction, $action);
    }

    public function testSettingNewDefaultValues()
    {
        $expectedModule     = 'home';
        $expectedController = 'foo';
        $expectedAction     = 'bar';

        $this->dispatcher->setDefaultModule($expectedModule);
        $this->dispatcher->setDefaultController($expectedController);
        $this->dispatcher->setDefaultAction($expectedAction);

        $module = $this->dispatcher->getDefaultModule();
        $controller = $this->dispatcher->getDefaultController();
        $action = $this->dispatcher->getDefaultAction();

        $this->assertSame($expectedModule, $module);
        $this->assertSame($expectedController, $controller);
        $this->assertSame($expectedAction, $action);
    }

    public function testFormatModuleName()
    {
        $expected = 'Foobar';
        $result = $this->dispatcher->formatModuleName('fOOBaR');

        $this->assertSame($expected, $result);
    }

    public function testFormatControllerName()
    {
        $expected = 'FoobarController';
        $result = $this->dispatcher->formatControllerName('FoObAr');

        $this->assertSame($expected, $result);
    }

    public function testFormatActionName()
    {
        $expected = 'foobarAction';
        $result = $this->dispatcher->formatActionName('fOObAR');

        $this->assertSame($expected, $result);
    }

    public function testFormatClassToFileName()
    {
        $expected = 'IndexController.php';
        $result = $this->dispatcher->classToFilename('IndexController');

        $this->assertSame($expected, $result);
    }

    public function testIsValidModule()
    {
        $this->dispatcher->setModuleDirectory(TESTPATH.'Nova/Controller/Dispatcher/_files/Modules/');

        $this->assertTrue($this->dispatcher->isValidModule('Test'));
        $this->assertFalse($this->dispatcher->isValidModule('Foobar'));
    }

    public function testIsDispatchable()
    {
        $request = new Request();
        $request->setModuleName('Test')->setControllerName('Test');
        $this->dispatcher->setModuleDirectory(TESTPATH.'Nova/Controller/Dispatcher/_files/Modules/');

        $this->assertTrue($this->dispatcher->isDispatchable($request));

        $request->setControllerName('Trololo');
        $this->assertFalse($this->dispatcher->isDispatchable($request));
    }

    public function testLoadController()
    {
        $request = new Request();
        $request->setModuleName('Test')->setControllerName('Test');
        $this->dispatcher->setDispatchDirectory(TESTPATH.'Nova/Controller/Dispatcher/_files/Modules/Test/Controller/');

        $this->assertFalse(class_exists('TestController', false));
        $this->dispatcher->loadController('TestController');
        $this->assertTrue(class_exists('TestController', false));
        unset($request);
    }

    public function testDispatching()
    {
        $request = new Request();
        $request->setModuleName('Test')->setControllerName('Test')->setActionName('test');
        $this->dispatcher->setModuleDirectory(TESTPATH.'Nova/Controller/Dispatcher/_files/Modules/');
        
        $response = new Response();

        $this->dispatcher->dispatch($request, $response);
        $result = $this->dispatcher->getResponse()->getBody();
        $this->assertContains('Test passed', $result);
        unset($request, $response);
    }

    public function testExceptioOnDispatchingInvalidModule()
    {
        $request = new Request();
        $request->setModuleName('Fake');
        $this->dispatcher->setModuleDirectory(TESTPATH.'Nova/Controller/Dispatcher/_files/Modules/');

        $response = new Response();

        $this->setExpectedException('\Nova\Controller\Dispatcher\Exception', 'Invalid Module');
        $this->dispatcher->dispatch($request,$response);
        unset($request, $response);        
    }

    public function testExceptionOnDispatchingValidModuleInvalidController()
    {
        $request = new Request();
        $request->setModuleName('Test')->setControllerName('Fake');
        $this->dispatcher->setModuleDirectory(TESTPATH.'Nova/Controller/Dispatcher/_files/Modules/');

        $response = new Response();

        $this->setExpectedException('\Nova\Controller\Dispatcher\Exception', 'Controller File: FakeController not found');
        $this->dispatcher->dispatch($request,$response);
        unset($request, $response);      
    }

    public function testExceptionOnDispatchingValidControllerFileInvalidControllerClass()
    {
        $request = new Request();
        $request->setModuleName('Test')->setControllerName('Other');
        $this->dispatcher->setModuleDirectory(TESTPATH.'Nova/Controller/Dispatcher/_files/Modules/');

        $response = new Response();

        $this->setExpectedException('\Nova\Controller\Dispatcher\Exception', 'Class: OtherController not found');
        $this->dispatcher->dispatch($request,$response);
        unset($request, $response);  
    }

    public function testExceptionOnDispatchingInvalidAction()
    {
        $request = new Request();
        $request->setModuleName('Test')->setControllerName('Test')->setActionName('Invalid');
        $this->dispatcher->setModuleDirectory(TESTPATH.'Nova/Controller/Dispatcher/_files/Modules/');

        $response = new Response();

        $this->setExpectedException('\Nova\Controller\Dispatcher\Exception', 'invalidAction does not exist');
        $this->dispatcher->dispatch($request,$response);
        unset($request, $response); 
    }

    public function testControllerMustExtendActionController()
    {
        $request = new Request();
        $request->setModuleName('Test')->setControllerName('Third');
        $this->dispatcher->setModuleDirectory(TESTPATH.'Nova/Controller/Dispatcher/_files/Modules/');

        $response = new Response();

        $this->setExpectedException('\Nova\Controller\Dispatcher\Exception', 'Controller must extend Nova\Controller\Action');
        $this->dispatcher->dispatch($request,$response);
        unset($request, $response); 
    }

}