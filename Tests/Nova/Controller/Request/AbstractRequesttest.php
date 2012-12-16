<?php

namespace Nova\Controller\Request\Unittests;

use Nova\Controller\Request\Http as Request;


include BASEPATH. "Nova/Controller/Request/AbstractRequest.php";
include BASEPATH. "Nova/Controller/Request/Http.php";

Class AbstractRequesttest extends \PHPUnit_Framework_Testcase
{
	public function setup()
	{
		// Use Http request as a proxy
		$this->request = new Request();
	}

	public function tearDown()
	{
		$this->request = null;
	}

	public function testGetModuleName()
	{
		$this->assertSame(null, $this->request->getModuleName());

		$this->request->setModuleName("MachineGun");
		$this->assertSame("MachineGun", $this->request->getModuleName());

		$this->request->setModuleName("EddieGrant");
		$this->assertNotSame("MachineGun", $this->request->getModuleName());
	}

	public function testGetControllerName()
	{
		$this->assertSame(null, $this->request->getControllerName());

		$this->request->setControllerName("PooPing");
		$this->assertSame("PooPing", $this->request->getControllerName());

		$this->request->setControllerName("EddieGrant");
		$this->assertNotSame("PooPing", $this->request->getControllerName());		
	}

	public function testGetActionName()
	{
		$this->assertSame(null, $this->request->getActionName());

		$this->request->setActionName("PooPing");
		$this->assertSame("PooPing", $this->request->getActionName());

		$this->request->setActionName("Ubuntu");
		$this->assertNotSame("PooPing", $this->request->getActionName());		
	}

	public function testGetModulekey()
	{
		$this->assertSame("module", $this->request->getModuleKey());
		$this->request->setModuleKey("package");
		$this->assertSame("package", $this->request->getModuleKey());
	}

	public function testGetControllerKey()
	{
		$this->assertSame("controller", $this->request->getControllerKey());
		$this->request->setControllerKey("class");
		$this->assertSame("class", $this->request->getControllerKey());		
	}

	public function testGetActionKey()
	{
		$this->assertSame("action", $this->request->getActionKey());
		$this->request->setActionKey("method");
		$this->assertSame("method", $this->request->getActionKey());		
	}

	public function testGetSingleParam()
	{
		$testKey   = "Samsung" ;
		$testValue = "Fernseher";

		$this->request->setParam($testKey,$testValue);
		$this->assertSame("Fernseher", $this->request->getParam("Samsung"));

	}

	public function testGetParamReturnsNullOnNonExistingKey()
	{
		$testKey   = "Toshiba" ;
		$testValue = "Monitor";

		$this->request->setParam($testKey,$testValue);
		$this->assertSame("Monitor", $this->request->getParam("Toshiba"));

		$this->request->setParam($testKey, null);
		$this->assertSame(null , $this->request->getParam("Fernseher"));
	}

	public function testGetParamReturnsCustumDefaultValue()
	{
		$this->assertSame(null, $this->request->getParam("Test"));
		$this->assertSame("value", $this->request->getParam("Test", "value"));
	}

	public function testSetMultipleParams()
	{
		$testArray = array("param1" => "test1", "param2" => "test2", "param3" => "test3");
		$this->request->setParams($testArray);

		$this->assertSame("test3", $this->request->getParam("param3"));
		$this->assertSame("test1", $this->request->getParam("param1"));
		$this->assertSame("test2", $this->request->getParam("param2"));

		$this->assertSame($testArray, $this->request->getParams());

		$testArrayTwo = array("param1" => null);
		$this->request->setParams($testArrayTwo);

		$this->assertNotSame($testArray, $this->request->getParams());
	}

	public function testClearParams()
	{
		$testArray = array("param1" => "test1", "param2" => "test2", "param3" => "test3");
		$this->request->setParams($testArray);

		$this->assertTrue(count($this->request->getParams()) == 3);

		$this->request->clearParams();
		$this->assertFalse(count($this->request->getParams()) == 3);
		$this->assertTrue(count($this->request->getParams()) == 0);
	}

	public function testSetIsDispatched()
	{
		$this->assertTrue($this->request->isDispatched());
		$this->request->setDispatched(false);
		$this->assertFalse($this->request->isDispatched());
	}

	public function testSetActionParamsdoesNotOverwriteOnNullValues()
	{
		$testArray = array("param1" => "test1", "param2" => "test2", "param3" => "test3");
		$this->request->setParams($testArray);

		$this->assertTrue(count($this->request->getParams()) == 3);
		$testArrayTwo = array("param4" => null, "param5" => null, "param6" => null);
		$this->request->setActionParams($testArrayTwo);
		$this->assertTrue(count($this->request->getParams()) == 6);
		$this->assertSame(null, $this->request->getParam("param4"));
	}

}