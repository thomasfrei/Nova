<?php 
/**
 * Nova - PHP 5 Framework
 *
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2012 Thomas Frei
 * @link        https://github.com/thomasfrei/nova
 * @license     https://github.com/thomasfrei/nova/blob/master/License.txt 
 * @package     Nova\Controller\Router
 * @version     0.0.1 
 *
 */
namespace Nova\Controller\Router;

use Nova\Controller\Request\AbstractRequest as Request;
use Nova\Controller\Dispatcher\Standard as Standard;

/**
 * Routes a Request to coresponding Module/Controller/Action
 *
 * @package  Nova\Controller
 * @subpackage Router
 */
Class Rewrite
{
	/**
	 * Instance of Dispatcher
	 * @var Standard
	 */
	protected $_dispatcher = null;

	/**
	 * Action params
	 * @var array
	 */
	protected $_params = array();

	/**
	 * Route a Request
	 *
	 * @todo  Rewrite and split into multiple functions
	 * @param  Request $request
	 * @return Rewrite       
	 */
	public function route(Request $request)
	{
		// Instantiate Default Dispatcher
		if($this->_dispatcher === null){
			$this->_dispatcher = new Standard();
		}
		// Get The Request Uri
		$requestUri = $request->getRequestUri();
		// Get The Base Uri
		$baseUri = $request->getBaseUri();

		// Compare RequestUri with BaseUri

		// with index.php in uri
		$pos = strpos($baseUri, $requestUri);

		if (strpos($requestUri, $baseUri) === 0) {
                    $new = substr($requestUri, strlen($baseUri));
		} else {
			// Without index.php in Uri
			$baseDir = dirname($baseUri);
		
			$pos = strpos($baseDir, $requestUri);

			if (strpos($requestUri, $baseDir) === 0) {
                    $new = substr($requestUri, strlen($baseDir));
			}
		}

		// index.php/
		if($new === '/' || empty($new)){
			$module = $this->_dispatcher->getDefaultModule();
			$controller = $this->_dispatcher->getDefaultController();
			$action = $this->_dispatcher->getDefaultAction();
		} else {

			$parts = array();
			$new = rtrim($new, "/");
			$parts = explode('/', $new);

			array_shift($parts);

			// Check for Module			
			if(isset($parts[0])){

				if(is_dir(APPPATH . ucfirst(strtolower($parts[0])))){
					$module = $parts[0];
					array_shift($parts);
				} else {
					$module = $this->_dispatcher->getDefaultModule();
				}

			} else {
				$module = $this->_dispatcher->getDefaultModule();
			}
		
			// Check for Controller
			if(isset($parts[0])){
				$controller = $parts[0];
			} else {
				$controller = $this->_dispatcher->getDefaultController();
			}

			array_shift($parts);

			// Check for action
			if(isset($parts[0])){
				$action = $parts[0];
			} else {
				$action = $this->_dispatcher->getDefaultAction();
			}

			array_shift($parts);

			// Check for Params
			$params = array();

			for ($i=0; $i<sizeof($parts); $i=$i+2) {
            	$params[$parts[$i]] = isset($parts[$i+1]) ? $parts[$i+1] : Null;
        	}

        	$this->_params = $params;
		}

		$request->setModuleName($module);
		$request->setControllerName($controller);
		$request->setActionName($action);
		$request->setActionParams($this->_params);

		return $this;
	}
}