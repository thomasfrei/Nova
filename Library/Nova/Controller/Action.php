<?php
/**
 * Nova - PHP 5 Framework
 *
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2012 Thomas Frei
 * @link        https://github.com/thomasfrei/nova
 * @license 	https://github.com/thomasfrei/nova/blob/master/License.txt 
 * @package     Nova\Controller
 * @version     0.0.1 
 */

		
namespace Nova\Controller;

use Nova\View as View;
use Nova\Controller\Request\AbstractRequest as AbstractRequest;
use Nova\Controller\Response\AbstractResponse as AbstractResponse;

/**
 * Base Class for Action Controllers
 * 
 * @package Nova\Controller
 */
abstract class Action{

	/**
	 * The Request Object
	 * @var AbstractRequest
	 */
	protected $_request = null;

	/**
	 * The Response Object
	 * @var AbstractResponse
	 */
	protected $_response = null;

	/**
	 * The view Object
	 * @var Nova\View
	 */
	public $view = null;

	/**
	 * Contructor
	 * 
	 * @param AbstractRequest  $request 
	 * @param AbstractResponse $response
	 */
	public function __construct(AbstractRequest $request, AbstractResponse $response)
	{
		$this->setRequest($request);
		$this->setResponse($response);
		$this->initview();


		// init the User Application Controller
		$methods = get_class_methods($this);

		if(in_array("_init",$methods)){
			$this->_init();
		}
		
	}

	/**
	 * Sets the Request
	 * 
	 * @param AbstractRequest $request 
	 */
	public function setRequest(AbstractRequest $request)
	{
		$this->_request = $request;
	}

	/**
	 * Set the Response
	 * 
	 * @param AbstractResponse $response
	 */
	public function setResponse(AbstractResponse $response)
	{
		$this->_response = $response;
	}

	/**
	 * Initialise the view 
	 *
	 * @return Nova\Controller\Action
	 */
	public function initView()
	{
		//
		$module = ucfirst($this->_request->getModuleName());
		$controller = $this->_request->getControllerName();
		$viewFile = APPPATH . $module . "/Views/" . $controller . ".php";

		$this->view = new View($viewFile);
		return $this;
	}

	/**
	 * Magic Function to set view Variables
	 *
	 * @param string $key
	 * @param string $value
	 */
	public function __set($key, $value)
	{
		$this->view->$key = $value;
	}

	/**
	 * Destructor
	 *
	 * Render the view and pass it to the response
	 */
	public function __destruct()
	{
		$content = $this->view->render();
		
		$this->_response->setBody($content);
	}
}