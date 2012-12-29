<?php

namespace Nova\Controller\Dispatcher\Unittests;

use Nova\Controller\Dispatcher\Standard as Standard;
use Nova\Controller\Request\Http as Request;
use Nova\Controller\Response\Http as Response;

Class AbstractDispatchertest extends \PHPUnit_Framework_Testcase
{
    public function setup()
    {
        $this->dispatcher = new Standard();
    }

    public function tearDown()
    {
        unset($this->dispatcher);
    }

    public function testGetSetResponse()
    {
        $response = new Response();
        $this->dispatcher->setResponse($response);

        $this->assertTrue($response === $this->dispatcher->getResponse());

        unset($response);
        $response = new Response();

        $this->assertFalse($response === $this->dispatcher->getResponse());
    }

    public function testDefaultModuleDirectory()
    {
        $expected = 'Modules';
        $result = $this->dispatcher->getModuleDirectory();
        $this->assertSame($result, $expected);

        $expected = TESTPATH;
        $this->dispatcher->setModuleDirectory($expected);
        $result = $this->dispatcher->getModuleDirectory();
        $this->assertSame($result, $expected);
    }

    public function testSetControllerDirectory()
    {
        $expected = TESTPATH.'Nova/Controller/Dispatcher/_files/Modules/Test/Controller/';
        $this->dispatcher->SetControllerDirectory($expected);

        $result = $this->dispatcher->getControllerDirectory();
        $this->assertSame($expected, $result);
    }

    public function testSetGetDispatchDirectory()
    {
        $expected = 'Path/To/Dispatch/Dir';
        $this->dispatcher->setDispatchDirectory($expected);

        $result = $this->dispatcher->getDispatchDirectory();
        $this->assertSame($expected, $result);
    }
}