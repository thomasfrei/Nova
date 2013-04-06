<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     Controller\Action\Helpers    
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace Nova\Controller\Action\Helper;

use Nova\View as View;
use Nova\Controller\Action\Helper\AbstractHelper as AbstractHelper;

/**
 * Automatic View Rendering
 *
 * @package     Controller\Action\Helpers      
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Class ViewRenderer extends AbstractHelper
{
	/**
	 * View
	 * @var View
	 */
	public $view = null;

	/**
	 * Shoul the request be rendered
	 * @var bool
	 */
	protected $_noRender = false;

	/**
	 * The view script to render
	 * @var string
	 */
	protected $_viewScript;

	/**
	 * Intialise the View
	 * 
	 * @return void
	 */
	public function init()
	{
		$this->initView();
	}

	/**
	 * Initialise the View Object
	 * 
	 * @return void
	 */
	public function initView()
	{
		if ($this->view === null) {
			$this->setView(new View());
		}

		// Get the base path
		$basePath = $this->getViewBasepath();

		// Set the Base Path in the View Object
		$this->view->setViewBasePath($basePath);

		$controller = $this->getRequest()->getControllerName();
		$action = $this->getRequest()->getActionName();

		// Set the view Script
		$this->_viewScript = $controller . '/' . $action . '.php';

		// Register the View Object with the action Controller
        $this->getActionController()->view = $this->view;
	}

	/**
	 * Set the View Object
	 * 
	 * @param Nove\View $view
	 * @return ViewRenderer
	 */
	public function setView($view)
	{
		$this->view = $view;
		return $this;
	}

	/**
	 * Auto renders the view
	 * 
	 * @return void
	 */
	public function postDispatch()
	{
		if (!$this->_noRender){
			$this->render();
		}
	}

	/**
	 * 
	 */
	public function setNoRender($flag = false)
	{
		$this->_noRender = ($flag) ? true : false;
		return $this;
	}

	/**
	 * Render the View
	 * 
	 * @return void
	 */
	public function render()
	{
		$this->getResponse()->appendBody(
			$this->view->render($this->_viewScript)
		);
	}

	/**
	 * Get the View Base Path
	 * 
	 * @return string 
	 */
	public function getViewBasePath()
	{
		$path = APPPATH. 'Modules' .DIRECTORY_SEPARATOR;
		$module = ucfirst($this->getRequest()->getModuleName());
        $basePath = $path. $module.DIRECTORY_SEPARATOR.'View';
        return $basePath;
	}
}