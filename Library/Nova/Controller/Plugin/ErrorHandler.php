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

namespace Nova\Controller\Plugin;

use Nova\Controller\Request\AbstractRequest as AbstractRequest;
use Nova\Controller\Response\AbstractResponse as AbstractResponse;

/**
 * Handles any Exceptions thrown in the Dispatch loop
 *
 * @package Nova\Controller
 * @subpackage Plugin
 */
Class ErrorHandler extends AbstractPlugin
{
	/**
	 * Error Module
	 * @var string
	 */
	protected $_errorModule = "error";

	/**
	 * Error Conrtroller
	 * @var string
	 */
	protected $_errorController = "error";

	/**
	 * Error Action
	 * @var string
	 */
	protected $_errorAction = "error";


	/**
	 * Called after the action is dispatched by the dispatcher
	 *
	 * @param AbstractRequest
	 * @return void 
	 * @throws Nova\Controller\Plugin\Exception
	 */
	public function postDispatch(AbstractRequest $request)
	{
		$this->_handleError($request);
	}

	/**
	 * Handle any Exceptions thrown 
	 *
	 * @todo Log Errors
	 * @todo  Different Error Codes: ROUTE_NOT_FOUND,INVALID_CONTROLLER usw
	 * @todo  Display a nice Error message 
	 * @param  AbstractRequest $request
	 * @return void
	 */
	protected function _handleError(AbstractRequest $request)
	{
		if($this->getResponse()->hasException()){
			
			$this->getRequest()->setModuleName($this->_errorModule);
			$this->getRequest()->setControllerName($this->_errorController);
			$this->getRequest()->setActionName($this->_errorAction);



			$exceptions = $this->_response->getException();
			$msg = $exceptions[0]->getMessage();
			$this->_response->SetHttpResponseCode(404);
			
			$this->getResponse()->removeFirstException();

			var_dump($exceptions);


		}		
	}
}