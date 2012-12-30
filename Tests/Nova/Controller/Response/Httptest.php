<?php

namespace Nova\Controller\Response\Unittests;

use Nova\Controller\Response\Http as Response;
use Nova\Controller\Response\AbstractResponse as AbstractResponse;

require_once BASEPATH."Nova/Controller/Response/AbstractResponse.php";
require_once BASEPATH."Nova/Controller/Response/Http.php";
require_once BASEPATH."Nova/Controller/Response/Exception.php";

Class Httptest extends \PHPUnit_Framework_Testcase
{
    public function setup()
    {
        $this->response = new Response();
    }

    public function tearDown()
    {
        unset($this->response);
    }

    public function testDefaultHttpResponseCodeIsCorrect()
    {
        $this->assertEquals(200, $this->response->getHttpResponseCode());
    }

    public function testRenderExceptions()
    {
        $this->assertFalse($this->response->renderExceptions());
        $this->assertTrue($this->response->renderExceptions(true));
        $this->assertTrue($this->response->renderExceptions());
        $this->assertFalse($this->response->renderExceptions(false));
        $this->assertFalse($this->response->renderExceptions());
    }

    public function testExceptions()
    {
        $this->assertFalse($this->response->hasException());
        $e = new \Exception('Test Exception');

        $this->response->setException($e);
        $this->assertTrue($this->response->hasException());

        $result = $this->response->getException();
        $this->assertCount(1, $result);

        $found = false;
        foreach($result as $exception){
            if($exception === $e){
                $found = true;
            }
        }

        $this->assertTrue($found);

        $this->response->removeFirstException();
        $result = $this->response->getException();
        $this->assertCount(0, $result);

    }

    public function testSetGetHttpResponseCode()
    {
        $this->response->setHttpResponseCode(400);
        $this->assertEquals(400,$this->response->getHttpResponseCode());

        $this->response->setHttpResponseCode(404);
        $this->assertEquals(404,$this->response->getHttpResponseCode());
    }

    public function testSettingAndNormalizingHeaders()
    {
        $expected = array(array('name' => 'Content-Type', 'value' => 'text/html', 'replace' => false));
        $this->response->setHeader('content-type', 'text/html',false);
        $result = $this->response->getHeaders();
        $this->assertSame($expected, $result);

        $expected[] = array('name'  =>  'Accept-Encoding', 'value' =>  'gzip', 'replace' => false);
        $this->response->setHeader('accept-encoding', 'gzip', false);
        $result = $this->response->getHeaders();
        $this->assertSame($expected, $result);

        $expected = array(array('name' => 'Content-Type', 'value' => 'text/xml', 'replace' => false));
        $this->response->setHeader('content-type', 'text/xml', true);
        $result = $this->response->getHeaders();

        $this->assertCount(2,$result);

        foreach($result as $header){
            if($header['name'] === 'Content-Type'){
                $this->assertSame($header['value'], 'text/xml');
                return; 
            } 
        }
    }

    public function testClearingHeaders()
    {
        $this->response->clearHeader('content-type');
        $this->assertCount(0, $this->response->getHeaders());

        $this->response->setHeader('content-type', 'text/html', false);
        $this->response->setHeader('accept-encoding', 'gzip', false);
        $this->assertCount(2, $this->response->getHeaders());

        $this->response->clearHeader('content-type');
        $this->assertCount(1, $this->response->getHeaders());

        $this->response->setHeader('content-type', 'text/html', false);
        $this->response->clearHeaders();
        $this->assertCount(0, $this->response->getHeaders());
    }

    public function testPrepend()
    {
        $this->response->setBody('some content', 'test');
        $this->response->prepend('first', "first content");

        $expected = array('first' => 'first content', 'test' => 'some content');
        $result = $this->response->getBody(true);
        $this->assertSame($expected, $result);

        $this->response->prepend('1', 'more');
        $expected = array('1' => 'more','first' => 'first content', 'test' => 'some content');
        $result = $this->response->getBody(true);
        $this->assertSame($expected, $result);

        $this->response->prepend('first', 'Trololo');
        $expected = array('first' => 'Trololo','1' => 'more', 'test' => 'some content');
        $result = $this->response->getBody(true);
        $this->assertSame($expected, $result);

    }

    public function testAppend()
    {
        $this->response->setBody('some content', 'test');
        $this->response->append('more', 'more content');

        $expected = array('test' => 'some content', 'more' => 'more content');
        $result = $this->response->getBody(true);
        $this->assertSame($expected, $result);

        $this->response->append('more', 'other content');
        $expected = array('test' => 'some content', 'more' => 'other content');
        $result = $this->response->getBody(true);
        $this->assertSame($expected, $result);
    }

    public function testAppendBodyDefaultSegment()
    {
        $this->response->appendBody('Start-');

        $this->response->setBody('Original Content');
        $this->response->appendBody(' - and more');

        $expected = 'Original Content - and more';
        $result = $this->response->getBody();
        $this->assertSame($expected, $result);
    }

    public function testAppendBodyNamedSegment()
    {
        $this->response->setBody('Page Title', 'title');
        $this->response->appendBody(' - Yolo', 'title');

        $expected = 'Page Title - Yolo';
        $result = $this->response->getBody();
        $this->assertSame($expected, $result);

        $this->response->appendBody('Content..' , 'content');
        $expected = array('title' => 'Page Title - Yolo', 'content' => 'Content..');
        $result = $this->response->getBody(true);
        $this->assertSame($expected, $result);
    }

    public function testGettingNamedBodySegment()
    {
        $this->response->append('first', 'first segment');
        $this->response->append('second', 'second segment');
        $this->response->append('third', 'third segment');

        $expected = 'second segment';
        $result = $this->response->getBody('second');
        $this->assertSame($expected, $result);

        $result = $this->response->getBody(array());
        $this->assertNull($result);
    }

    public function testSettingRedirect()
    {
        $this->response->setRedirect('http://www.example.com');

        $this->assertSame(302, $this->response->getHttpResponseCode());
        $result = $this->response->getHeaders();
        $this->assertCount(1,$result);

        $this->response->setRedirect('http://www.example.com/test');
        $result = $this->response->getHeaders();
        $this->assertCount(1,$result);

        $result = $this->response->getHeaders('Location');

        foreach($result as $header){
            if($header['name'] === 'Location'){
                $this->assertSame($header['value'], 'http://www.example.com/test');
                return; 
            } 
        }
    }
    
    /**
     * @requires extension xdebug
     * @runInSeparateProcess
     */
    public function testSendHeaders()
    {   
        ob_start();
        $this->response->setHeader('content-type', 'text/html',false);
        $this->response->setHeader('content-type', 'text/xml', true);
        $this->response->setRedirect('http://www.example.com/test');
        $this->response->sendHeaders();
        $headers = xdebug_get_headers();

        $this->assertNotEmpty($headers);
        $this->assertContains('Location:http://www.example.com/test', $headers);
        $this->assertContains('Content-Type:text/xml', $headers);
        header_remove();
        ob_end_flush();
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendResponse()
    {
        $this->response->setBody('Page Content', 'content');
        

        ob_start();
        $this->response->sendResponse();
        $result = ob_get_clean();

        $this->assertNotEmpty($result);
        $this->assertEquals('Page Content', $result);
        
        $e = new \Exception('Test Exception');
        $this->response->setException($e);
        $this->response->renderExceptions(true);
        
        ob_start();
        $this->response->sendResponse();
        $result = ob_get_clean();
        $this->assertStringStartsWith('exception \'Exception\' with message \'Test Exception\'', $result);
    }

     /**
     * @runInSeparateProcess
     */
    public function test__toString()
    {
        $this->response->setHeader('Content-Type', 'text/plain');
        $this->response->setBody('Content');
        $this->response->appendBody('; and more content.');
        $result = $this->response->__toString();
        $this->assertEquals('Content; and more content.', $result);
    }
}