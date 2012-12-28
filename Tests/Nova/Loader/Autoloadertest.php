<?php

namespace Nova\Loader\Unittests;

use Nova\Loader\Autoloader as Autoloader;

include BASEPATH."Nova/Loader/Autoloader.php";
include BASEPATH."Nova/Loader/Exception.php";

class AutoloaderTest extends \PHPUnit_Framework_Testcase
{
	public function setup()
	{
		$this->loader = new Autoloader();
	}

	public function tearDown()
	{
		$this->loader->unregister();
	}

	public function testDefaultValuesAreCorrect()
	{
		$expected = '.php';
		$this->assertSame($expected, $this->loader->getFileExtension());

		$expected = '\\';
		$this->assertSame($expected, $this->loader->getNamespaceSeparator());
	}

	public function testValuesAreChangedCorrectly()
	{
		$this->loader->setFileExtension('.html');
		$this->assertSame('.html', $this->loader->getFileExtension());

		$this->loader->setNamespaceSeparator('::');
		$this->assertSame('::', $this->loader->getNamespaceSeparator());
	}

	public function testChangingValueThroughConstructor()
	{
		unset($this->loader);

		$options = array(
			'file.extension' => '.xml',
			'namespace.separator' => '()',
			'include.path' => '/var/www/'
		);

		$this->loader = new Autoloader($options);

		$this->assertSame('()', $this->loader->getNamespaceSeparator());
		$this->assertSame('.xml', $this->loader->getFileExtension());

		$paths = explode(PATH_SEPARATOR, $this->loader->getIncludePath());
		$this->assertTrue(in_array('/var/www/', $paths));
	}

	public function testAutoloadersetsIncludePath()
	{
		$paths = explode(PATH_SEPARATOR, $this->loader->getIncludePath());
		$this->assertFalse(in_array(TESTPATH, $paths));
		$this->assertFalse(in_array(BASEPATH, $paths));

		$this->loader->setIncludePath(array(TESTPATH, BASEPATH));

		$paths = explode(PATH_SEPARATOR, $this->loader->getIncludePath());
		$this->assertTrue(in_array(TESTPATH, $paths));
		$this->assertTrue(in_array(BASEPATH, $paths));
	}

	public function testAutoloaderIsRegistered()
	{
		$this->loader->register();
		$loaders = spl_autoload_functions();

		foreach($loaders as $object => $method){
			$this->methods[] = $method;
		}

		$this->assertTrue(in_array(array($this->loader,"loadClass"), $this->methods));
	}

	public function testAutoloaderCanBeUnregistered()
	{
		$this->loader->unregister();
		$loaders = spl_autoload_functions();

		$this->assertFalse(in_array(array($this->loader,"loadClass"), $loaders));
	}

	public function testAutoloaderIsLoaded()
	{
		$method = new \ReflectionMethod($this->loader, 'isLoaded');
		$method->setAccessible(TRUE);
		$this->assertFalse($method->invoke($this->loader , "Nova\Loader\Foo"));
		$this->assertTrue($method->invoke($this->loader ,"Nova\Loader\Autoloader"));		
	}


	public function testClassGetsLoaded()
	{
		$method = new \ReflectionMethod($this->loader, 'loadClass');
		$method->setAccessible(TRUE);
		$this->assertTrue($method->invoke($this->loader ,"Loader\SampleFile"));
		$this->assertTrue($method->invoke($this->loader ,"Nova\Loader\Autoloader"));

		$this->setExpectedException('Nova\Loader\Exception');
		$this->assertFalse($method->invoke($this->loader , "Foo/Bar/Baz"));
	}

	public function testClassnameGetsCorrectlyTransformedToFilename()
	{
		$method = new \ReflectionMethod($this->loader, 'transformClassnameToFilename');
		$method->setAccessible(TRUE);
		$this->assertSame("Foo/Bar/Baz.php", $method->invoke($this->loader, "Foo\Bar\Baz"));
		$this->assertSame("Test.php", $method->invoke($this->loader, "Test"));
	}

	public function testRegisteringNamespaces()
	{
		$namespaces = array(
			'Namespace\One' => 'path/to/namespace/one',
	  		'Namespace\Two' => 'path/to/namespace/one/two'
		);

		$this->loader->registerNamespaces($namespaces);
		$result = $this->loader->getRegisteredNamespaces();
		$this->assertSame($result, $namespaces);

		$this->setExpectedException('Nova\Loader\Exception');
		$this->loader->registerNamespaces(array('Namespace\One' => 'path/to/namespace/two'));
	}

	public function testUnregisteringNamespaces()
	{
		$namespaces = array(
			'Namespace\One' => 'path/to/namespace/one',
	  		'Namespace\Two' => 'path/to/namespace/one/two'
		);

		$expected = array('Namespace\Two' => 'path/to/namespace/one/two');

		$this->loader->registerNamespaces($namespaces);
		$this->loader->unregisterNamespace('Namespace\One');
		$result = $this->loader->getRegisteredNamespaces();

		$this->assertNotSame($namespaces, $result);
		$this->assertSame($expected, $result);
	}
}