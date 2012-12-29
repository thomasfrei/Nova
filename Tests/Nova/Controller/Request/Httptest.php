<?php

namespace Nova\Controller\Request\Unittests;

use Nova\Controller\Request\Http as Request;
use Nova\Controller\Request\AbstractRequest as AbstractRequest;

require_once BASEPATH."Nova/Controller/Request/AbstractRequest.php";
require_once BASEPATH."Nova/Controller/Request/Http.php";
require_once BASEPATH.'Nova/Controller/Plugin/PluginInterface.php';
require_once BASEPATH."Nova/Controller/Exception.php";
require_once BASEPATH."Nova/Controller/Request/Exception.php";

Class Httptest extends \PHPUnit_Framework_Testcase
{
	public function setup()
	{
		$this->_request = new Request();
	}

	public function teardown()
	{
		unset($this->_request);
	}

	public function testRequestUriCanBePassedWithConstructor()
	{
		unset($this->_request);
		$this->_request = new Request('even/steven/96');
		$this->assertEquals('even/steven/96', $this->_request->getRequestUri());
	}

	public function testDiscoversUriAtomaticliIfNoUriIsPassedThruContructor()
	{
		unset($this->_request);
		$_SERVER["REQUEST_URI"] = "/Store/Cars/Ferrari";
		$this->_request = new Request();
		$this->assertSame("/Store/Cars/Ferrari", $this->_request->getRequesturi());
	}
	
	public function testRequestUriCanBeOverWritten()
	{
		unset($this->_request);
		$this->_request = new Request('Hablo/Espanol');
		$this->_request->setRequestUri('no/hablo/espanol');
		$this->assertNotEquals('Hablo/Espanol' , $this->_request->getRequestUri());
		$this->assertEquals('no/hablo/espanol', $this->_request->getRequesturi());
	}

	public function testGettingRequestValues()
	{
		// Should return entire $_SERVER array if no key is passed
		$this->assertEquals($_SERVER, $this->_request->getServer());

		$_SERVER["HTTP_ACCEPT_CHARSET"] = "Khalisi";
		$this->assertEquals("Khalisi", $this->_request->getServer("HTTP_ACCEPT_CHARSET"));
		
		// Should return null if unknown $key is passed
		$this->assertEquals(null, $this->_request->getServer("USER_MOOD"));			
	}

	public function testgetBaseUri()
	{
		unset($this->_request);
		$_SERVER['SCRIPT_NAME'] = '/Nova/public/index.php';
		$_SERVER['PHP_SELF'] = '/Nova/public/index.php';

		$this->_request = new Request();
		$this->assertSame('/Nova/public/index.php', $this->_request->getBaseUri());

		$this->_request->setBaseUri('/www/home/index.php');
		$this->assertSame('/www/home/index.php', $this->_request->getBaseUri());

		unset($this->_request);
		unset($_SERVER['SCRIPT_NAME']);

		$this->_request = new Request();
		$this->assertSame('/Nova/public/index.php', $this->_request->getBaseUri());
	}

	public function testGetMethod()
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$this->assertSame($this->_request->getMethod(),'GET');

		$_SERVER['REQUEST_METHOD'] = 'POST';
		$this->assertSame($this->_request->getMethod(),'POST');

	}

	public function testIsPost()
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$this->assertFalse($this->_request->isPost());

		$_SERVER['REQUEST_METHOD'] = 'POST';
		$this->assertTrue($this->_request->isPost());
	}

	public function testIsGet()
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$this->assertTrue($this->_request->isGet());

		$_SERVER['REQUEST_METHOD'] = 'POST';
		$this->assertFalse($this->_request->isGet());
	}

	public function testGetPost()
	{
		$_POST['testvalue1'] = '123456';
		$this->assertSame('123456', $this->_request->getPost('testvalue1'));

		$_POST['testvalue2'] = '654321';
		$this->assertSame('654321', $this->_request->getPost('testvalue2'));

		$expected = array('testvalue1' => '123456', 'testvalue2' => '654321');
		$actual = $this->_request->getPost(null);

		$this->assertSame($expected, $actual);
	}

	public function testGetCookie()
	{
		$_COOKIE['username'] = 'hans';
		$this->assertSame('hans', $this->_request->getCookie('username'));

		$_COOKIE['user-title'] = 'Admin';
		$this->assertSame('Admin', $this->_request->getCookie('user-title'));

		$expected = array('username' => 'hans', 'user-title' => 'Admin');
		$actual = $this->_request->getCookie(null);

		$this->assertSame($expected, $actual);
	}
}