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

use Nova\Http\Request as Request;

/**
 * Request Tests
 *
 * @package     Nova\Http
 * @subpackage  NovaTests
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Class RequestTest extends \PHPUnit_Framework_Testcase
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * Original Server
     * @var array
     */
    protected $originalServer;

    public function setup()
    {
        $this->originalServer = $_SERVER;

        $_SERVER['REQUEST_URI']     = 'http://www.example.com';
        $_SERVER['DOCUMENT_ROOT']   = '/var/www';
        $_SERVER['REQUEST_METHOD']  = 'POST';

        $this->request = new Request();
    }

    public function tearDown()
    {
        $_SERVER = $this->originalServer;
        unset($this->request);
    }

    public function testGetsAndSetsRequestUri()
    {
        $this->assertSame('http://www.example.com', $this->request->getRequestUri());

        $this->request->setRequestUri('http://www.example.com/index/foo');
        $this->assertSame('http://www.example.com/index/foo', $this->request->getRequestUri());
    }

    public function testDetectsRequestMethod()
    {
        $this->assertSame('POST', $this->request->getMethod());
    }

    public function testDetectsIfARequestIsPost()
    {
        $this->assertTrue($this->request->isPost());

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertFalse($this->request->isPost());
    }

    public function testDetectsIfARequestIsGet()
    {
        $this->assertFalse($this->request->isGet());

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertTrue($this->request->isGet());
    }

    public function testRetrievesSingleValueFromServerGlobal()
    {
        $this->assertSame('/var/www', $this->request->getServer('DOCUMENT_ROOT'));
        $this->assertSame('http://www.example.com', $this->request->getServer('REQUEST_URI'));
    }

    public function testRetrievesEntireServerArray()
    {
        $result = $this->request->getServer();

        $this->assertArrayHasKey('REQUEST_URI', $result);
        $this->assertSame('http://www.example.com', $result['REQUEST_URI']);
        $this->assertArrayHasKey('DOCUMENT_ROOT', $result);
        $this->assertSame('/var/www', $result['DOCUMENT_ROOT']);
    }

    public function testRetrievesSingleValueFromPostGlobal()
    {
        $_POST['testvalue1'] = '123456';
        $this->assertSame('123456', $this->request->getPost('testvalue1'));
        
        $_POST['foo'] = 'bar';
        $this->assertSame('bar', $this->request->getPost('foo'));
    }

    public function testRetrievesEntirePostArray()
    {
        $_POST['foo'] = 'bar';
        $_POST['bar'] = 'baz';

        $result = $this->request->getPost();
        
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('foo', $result);
        $this->assertArrayHasKey('bar', $result);
        $this->assertSame('bar', $result['foo']);
        $this->assertSame('baz', $result['bar']);
    }

    public function testSetsTheBaseUri()
    {
        $_SERVER['SCRIPT_NAME'] = '/www/home/index.php';
        $_SERVER['PHP_SELF']    = '/www/home/index.php';

        $request = new Request();

        $this->assertSame('/www/home/index.php', $request->getBaseUri());
        unset($_SERVER['SCRIPT_NAME']);

        $request->setBaseUri(null);
        $this->assertSame('/www/home/index.php', $request->getBaseUri());

        $request->setBaseUri('base/uri');
        $this->assertSame('base/uri', $request->getBaseUri());
    }

    public function testConstructorAutomaticallySetsBaseAbdRequestUri()
    {
        $_SERVER['SCRIPT_NAME'] = '/www/home/index.php';

        $request = new Request('http://localhost/home');
        $this->assertSame('http://localhost/home', $request->getRequestUri());
        $this->assertSame('/www/home/index.php', $request->getBaseUri());
    }

}