<?php

namespace Nova\Loader\Unittests;

use Nova\Loader\Autoloader as Autoloader;

include BASEPATH. "Nova/Loader/Autoloader.php";

class AutoloaderTest extends \PHPUnit_Framework_Testcase
{
	public function setup()
	{
		$this->loader = new Autoloader();
		$this->loader->setIncludePath(array(TESTPATH, BASEPATH));
	}

	public function tearDown()
	{
		$this->loader->unregisterAutoloader();
	}

	public function testAutoloadersetsIncludePath()
	{
		$paths = explode(':', get_include_path());
		$this->assertTrue(in_array(TESTPATH, $paths));
		$this->assertTrue(in_array(BASEPATH, $paths));

		$this->loader->setIncludePath(BASEPATH);

		$paths = explode(':', get_include_path());
		$this->assertFalse(in_array(TESTPATH, $paths));
		$this->assertTrue(in_array(BASEPATH, $paths));
	}

	public function testAutoloaderIsRegistered()
	{
		$this->loader->registerAutoloader(false, false);
		$loaders = spl_autoload_functions();

		foreach($loaders as $object => $method){
			$this->methods[] = $method;
		}

		$this->assertTrue(in_array(array($this->loader,"_loadClass"), $this->methods));
	}

	public function testAutoloaderCanBeUnregistered()
	{
		$this->loader->unregisterAutoloader();
		$loaders = spl_autoload_functions();

		$this->assertFalse(in_array(array($this->loader,"_loadClass"), $loaders));
	}

	public function testAutoloaderIsLoaded()
	{
		$method = new \ReflectionMethod($this->loader, '_isLoaded');
		$method->setAccessible(TRUE);
		$this->assertFalse($method->invoke($this->loader , "Nova\Loader\Foo"));
		$this->assertTrue($method->invoke($this->loader ,"Nova\Loader\Autoloader"));		
	}


	public function testClassGetsLoaded()
	{
		$method = new \ReflectionMethod($this->loader, '_loadClass');
		$method->setAccessible(TRUE);
		$this->assertTrue($method->invoke($this->loader ,"Loader\SampleFile"));
		$this->assertFalse($method->invoke($this->loader , "Foo/Bar/Baz"));
		$this->assertTrue($method->invoke($this->loader ,"Nova\Loader\Autoloader"));
	}

	public function testClassnameGetsCorrectlyTransformedToFilename()
	{
		$method = new \ReflectionMethod($this->loader, '_classToFilename');
		$method->setAccessible(TRUE);
		$this->assertSame("Foo/Bar/Baz.php", $method->invoke($this->loader, "Foo\Bar\Baz"));
		$this->assertSame("Test.php", $method->invoke($this->loader, "Test"));
	}
}