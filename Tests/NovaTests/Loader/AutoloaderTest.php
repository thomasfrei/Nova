<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     Nova\Loader
 * @subpackage  NovaTests
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace NovaTests\Loader;

use Nova\Loader\Autoloader as Autoloader;

/**
 * Autoloader
 *
 * @package     Nova\Loader
 * @subpackage  NovaTests
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Class AutoloaderTest extends \PHPUnit_Framework_Testcase
{
    /**
     * Setup the Test Environment
     */
    public function setup()
    {
        $this->loader = new Autoloader();

         // Grab the Original Include Path
        $this->tempPath = get_include_path();
    }

    /**
     * TearDown the Test Environment
     */
    public function tearDown()
    {
        // Restore Original Include Path
        set_include_path($this->tempPath);

        $this->loader->unregister();
        unset($this->loader);
    }

    public function testAutoloaderCanBeRegisteredWithSpl()
    {
        $this->loader->register();
        $loaders = spl_autoload_functions();

        foreach($loaders as $object => $method){
            $this->methods[] = $method;
        }
        $this->assertTrue(in_array(array($this->loader,"loadClass"), $this->methods));
    }

    public function testAutoloaderCanBeUnregisteredFromSplStack()
    {
        $this->loader->unregister();
        $loaders = spl_autoload_functions();
        $this->assertFalse(in_array(array($this->loader,"loadClass"), $loaders));
    }

    public function testDefaultFileExtensionIsPhp()
    {
        $expected = '.php';
        $this->assertSame($expected, $this->loader->getFileExtension());
    }

    public function testFileExtensionCanBeChanged()
    {
        $this->loader->setFileExtension('.html');
        $this->assertNotSame('.php', $this->loader->getFileExtension());
        $this->assertSame('.html', $this->loader->getFileExtension());
    }

    public function testDefaultNamespaceSeparatorIsBackslash()
    {
        $expected = '\\';
        $this->assertSame($expected, $this->loader->getNamespaceSeparator());
    }

    public function testNamespaceSeparatorCanBeChanged()
    {
        $this->loader->setNamespaceSeparator('::');
        $this->assertNotSame('\\', $this->loader->getNamespaceSeparator());
        $this->assertSame('::', $this->loader->getNamespaceSeparator());
    }

    public function testSetIncludePathAcceptsStrings()
    {
        $this->loader->setIncludePath('string/path');
        $result = explode(PATH_SEPARATOR, $this->loader->getIncludePath());
        $this->assertTrue(in_array('string/path', $result));
    }

    public function testSetIncludePathAcceptsArrays()
    {
        $this->loader->setIncludePath(array('some/path', 'other/path'));
        $result = explode(PATH_SEPARATOR, $this->loader->getIncludePath());
        $this->assertTrue(in_array('some/path', $result));
        $this->assertTrue(in_array('other/path', $result));
    }

    public function testSetIncludePathDoesNotOverwriteExistingIncludePath()
    {
        $this->loader->setIncludePath(array('some/path', 'other/path'));
        $result = explode(PATH_SEPARATOR, $this->loader->getIncludePath());
        $this->assertNotSame($this->tempPath, $result);
        $temp = explode(PATH_SEPARATOR,$this->tempPath);
        $this->assertContains($temp[0], $result);
        $this->assertContains('some/path', $result);
    }

    public function testAutoloaderOptionsCanBeSetThroughTheContructor()
    {
        $options = array(
            'file.extension' => '.xml',
            'namespace.separator' => '()',
            'include.path' => 'path/to/library'
        );

        $loader = new Autoloader($options);
        $paths = explode(PATH_SEPARATOR, $loader->getIncludePath());

        $this->assertSame('()', $loader->getNamespaceSeparator());
        $this->assertSame('.xml', $loader->getFileExtension());
        $this->assertTrue(in_array('path/to/library', $paths));
        unset($loader);
    }

    public function testCanDetectIfAClassIsAlreadyLoaded()
    {
        $method = new \ReflectionMethod($this->loader, 'isLoaded');
        $method->setAccessible(TRUE);
        $this->assertFalse($method->invoke($this->loader , "Nova\Loader\Foo"));
        $this->assertTrue($method->invoke($this->loader ,"Nova\Loader\Autoloader"));
    }

    public function testPsr0EachNamespaceSeparatorIsConvertedToADirectorySeparator()
    {
        $method = new \ReflectionMethod($this->loader, 'transformClassnameToFilename');
        $method->setAccessible(TRUE);
        $this->assertSame("Foo/Bar/Baz.php", $method->invoke($this->loader, "Foo\Bar\Baz"));
        $this->assertSame("Test.php", $method->invoke($this->loader, "Test"));
    }

    public function testPsr0EachUnderscoreInClassnameIsConvertedToADirectorySeparator()
    {
        $method = new \ReflectionMethod($this->loader, 'transformClassnameToFilename');
        $method->setAccessible(TRUE);
        $this->assertSame("namespace/package_name/Class/Name.php", $method->invoke($this->loader, "namespace\package_name\Class_Name"));
        $this->assertSame("namespace/packagename/Class/Name.php", $method->invoke($this->loader, "namespace\packagename\Class_Name"));
    }

    public function testPsr0AutoloaderIsNotCaseSensitiv()
    {
        $method = new \ReflectionMethod($this->loader, 'transformClassnameToFilename');
        $method->setAccessible(TRUE);
        $this->assertSame("namespace/PaCkaGeNaMe/ClASsNamE.php", $method->invoke($this->loader, "namespace/PaCkaGeNaMe/ClASsNamE"));
    }

    public function testNamespacesCanBeRegistered()
    {
        $namespaces = array(
            'Namespace\One' => 'path/to/namespace/one',
            'Namespace\Two' => 'path/to/namespace/one/two'
        );

        $this->loader->registerNamespaces($namespaces);
        $result = $this->loader->getRegisteredNamespaces();
        $this->assertSame($result, $namespaces);

        $this->setExpectedException('Nova\Loader\Exception','Namespace: Namespace\One already registered with different value');
        $this->loader->registerNamespaces(array('Namespace\One' => 'path/to/namespace/two'));
    }

    public function testNamespacesCanBeUnregistered()
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

    public function testAutoloaderLoadsFiles()
    {
        $path = (dirname(__FILE__));
        $this->loader->setIncludePath($path);

        $method = new \ReflectionMethod($this->loader, 'loadClass');
        $method->setAccessible(TRUE);

        $this->assertTrue($method->invoke($this->loader ,"_testfiles\SampleFile"));
        $this->assertTrue($method->invoke($this->loader ,"Nova\Loader\Autoloader"));

        $this->setExpectedException('Nova\Loader\Exception', 'File Foo/Bar/Baz.php could not be loaded');
        $this->assertFalse($method->invoke($this->loader , "Foo/Bar/Baz"));
    }

    public function testAutoloaderFindsFilesWithNamespaces()
    {
        $namespace = array('_testfiles' => $path = (dirname(__FILE__)).'/_testfiles/');
        $this->loader->registerNamespaces($namespace);

        $method = new \ReflectionMethod($this->loader, 'loadClass');
        $method->setAccessible(TRUE);
        $this->assertTrue($method->invoke($this->loader ,"_testfiles\SampleFile"));
    }
}