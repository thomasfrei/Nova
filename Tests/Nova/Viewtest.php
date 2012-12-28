<?php

namespace Nova\Unittests;

use Nova\View as View;

include BASEPATH."Nova/View/AbstractView.php";
include BASEPATH."Nova/View.php";
include BASEPATH.'Nova/View/Exception.php';
include BASE.'Tests/Nova/View/Helper/HelperStub.php';

Class ViewTest extends \PHPUnit_Framework_Testcase
{
    public function setup()
    {
        $this->view = new View();
    }

    public function tearDown()
    {
        unset($this->view);
    }

    public function testInitialValues()
    {
        $this->assertEquals('htmlspecialchars', $this->view->getEscape());
        $this->assertEquals('UTF-8', $this->view->getEncoding());
    }

    public function testSettingOptionsThroughContructor()
    {
        $options = array(
            'escape' => 'htmlentities',
            'encoding' => 'ISO-8859-1'
        );

        $this->view = new View($options);

        $this->assertEquals('htmlentities', $this->view->getEscape());
        $this->assertEquals('ISO-8859-1', $this->view->getEncoding());

        unset($this->view);
    }

    public function testSetandGet()
    {
        $this->view->test = 'test-value';

        $result = $this->view->getVars();
        $this->assertTrue(array_key_exists('test', $result));
        $this->assertEquals('test-value', $result['test']);
        $this->assertEquals('test-value', $this->view->test);
    }

    public function testSettingPrivateOrProtectedMembersThrowsException()
    {
        $this->setExpectedException('Nova\View\Exception');
        $this->view->_path = 'path/to/something';
    }

    public function testIssetEmpty()
    {
        $this->assertFalse(isset($this->view->foo));
        $this->assertTrue(empty($this->view->foo));

        $this->view->foo = 'bar';

        $this->assertTrue(isset($this->view->foo));
        $this->assertFalse(empty($this->view->foo));

        $this->assertFalse(isset($this->view->_private));
    }

    public function testUnset()
    {
        unset($this->view->test);
        $this->assertTrue(empty($this->view->test));
    }

    public function testSetScriptPath()
    {
        $this->assertNull($this->view->getScriptPath());

        $path = 'Path/To/My/Scripts';
        $this->view->setScriptPath($path);

        $result = $this->view->getScriptPath();

        $this->assertSame($path, $result);
    }

    public function testLoadHelper()
    {
        set_include_path(TESTPATH);
        $this->view->setHelperNamespace('Nova\Tests\View\Helper\\');
        $result = $this->view->HelperStub();

        $this->assertEquals('Test passed', $result);
    }

    public function testRender()
    {
        $this->view->setScriptPath(BASE.'Tests/Nova/View/Scripts/');
        ob_start();
        echo $this->view->render('test.php');
        $result = ob_get_clean();

        $this->assertSame('Test passed', $result);
    }

    public function testExceptionOnInvalidScriptName()
    {
        $this->view->setScriptPath(BASE.'Tests/Nova/View/Scripts/');

        $this->setExpectedException('Nova\View\Exception', 'Script nonexistingScript.php not found');
        $this->view->render('nonexistingScript.php');
    }

    public function testExceptionOnInvalidScriptPath()
    {
        $this->view->setScriptPath(BASE.'Invalid/Path');

        $this->setExpectedException('Nova\View\Exception', 'Invalid/Path/ not found');
        $this->view->render('nonexistingScript.php');
    }

    public function testGet()
    {
        $this->assertNull($this->view->haha);
    }

    public function testDefaultHelperNamespace()
    {
        $this->assertEquals('Nova\View\Helper\\', $this->view->getHelperNamespace());
    }

    public function testEscaping()
    {
        $value = 'You & me';

        $result = $this->view->escape($value);
        $this->assertEquals('You &amp; me', $result);
    }
}