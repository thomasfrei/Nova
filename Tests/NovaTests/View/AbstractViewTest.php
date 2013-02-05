<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     View
 * @subpackage  NovaTests    
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

 Namespace NovaTests\View;

 use Nova\View\AbstractView;

/**
 * AbstractView Test
 *
 * @package     View
 * @subpackage  NovaTests      
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Class AbstractViewTest extends \PHPUnit_Framework_Testcase
{
	public function setup()
	{
		$this->view = $this->getMockForAbstractClass('Nova\View\AbstractView');
	}

	public function tearDown()
	{
		unset($this->view);
	}

	public function testSetAndGetScriptPath()
	{
		$this->view->setScriptpath('Path/To/Script');
		$this->assertSame('Path/To/Script', $this->view->getScriptPath());
	}

	public function testSetAndGetEncoding()
	{
		$this->assertSame('UTF-8', $this->view->getEncoding());

		$this->view->setEncoding('UTF-24');
		$this->assertSame('UTF-24', $this->view->getEncoding());
	}

	public function testSetAndGetEscaping()
	{
		$this->assertSame('htmlspecialchars', $this->view->getEscape());

		$this->view->setEscape('htmlentities');
		$this->assertSame('htmlentities', $this->view->getEscape());
	}

	public function testSetAndGetHelperNamespace()
	{
		$this->assertSame('Nova\View\Helper\\', $this->view->getHelperNamespace());

		$this->view->setHelperNamespace('My\View\Helpers\\');
		$this->assertSame('My\View\Helpers\\', $this->view->getHelperNamespace());
	}

	public function test__set()
	{
		$this->view->testvar = 'foobar';

		$result = $this->view->getVars();

		$this->assertArrayHasKey('testvar', $result);
		$this->assertSame($result['testvar'], 'foobar');
	}

	public function test__setThrowsExceptionsWhenSettingProtectedVars()
	{
		$this->setExpectedException('Nova\View\Exception', 'Setting private or protected class members is not allowed');
		$this->view->_request = 'invalid';
	}

	public function test__get()
	{
		$this->assertNull($this->view->invalid);
	}

	public function test__isset()
	{
		$this->assertFalse(isset($this->view->invalid));
		$this->assertFalse(isset($this->view->_script));
	}

	public function testSetsOptionThroughConstructor()
	{
		unset($this->view);

		$options = array(
			'encoding' => 'WTF-13',
			'escape' => 'htmlentities'
		);

		$this->view = new TestView($options);

		$this->assertSame('WTF-13', $this->view->getEncoding());
		$this->assertSame('htmlentities', $this->view->getEscape());
	}

	public function testExcape()
	{
		$result = $this->view->escape('You & Me');
		$this->assertSame('You &amp; Me', $result);

		$this->view->setEscape('blabber');
		$result = $this->view->escape('You & Me');
		$this->assertNull($result);
	}
}

Class TestView extends AbstractView
{

	public function _run()
	{}
}
