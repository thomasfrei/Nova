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

namespace Nova\Controller\Request;

use Nova\Controller\Request\AbstractRequest as AbstractRequest;

/**
 * Http Request
 * 
 * @package 		Nova\Controller
 * @subpackage 		Request
 */
Class Http extends AbstractRequest
{
	/**
	 * The request uri
	 * @var string
	 */
	protected $_requestUri = null;

	/**
	 * The base uri 
	 * @var String
	 */
	protected $_baseUri = null;

	/**
	 * Contructor
	 *
	 * Automatically calls the setRequestUri and setBaseUri methods
	 *
	 * @param string $requestUri A 
	 * @return void
	 */
	public function __construct($requestUri = null)
	{
		$this->setRequestUri($requestUri);
		$this->setBaseUri();
	}

	/**
	 * Returns the requested uri
	 *
	 * @return string $this->_requestUri
	 */
	public function getRequestUri()
	{
		return $this->_requestUri;
	}

	/**
	 * Sets the Request Uri
	 *
	 * @param string 
	 * @return AbstractRequest
	 */
	public function setRequestUri($requestUri = null)
	{
		if ($requestUri != null){
			$this->_requestUri = $requestUri;
		} else {
			if (isset($_SERVER["REQUEST_URI"])){
				$this->_requestUri = $_SERVER["REQUEST_URI"];
			}
		}

		return $this;
	}

	/**
	 * Retrieve the base Uri
	 *
	 * @return string _baseUri
	 */
	public function getBaseUri()
	{
		return $this->_baseUri;
	}

	/**
	 * Sets the Base Uri
	 *
	 * @param  string $baseUri
	 * @return AbstractRequest
	 */
	public function setBaseUri($baseUri = null)
	{
		if($baseUri === null){
			if(($this->getServer("SCRIPT_NAME")) !== null){
				$this->_baseUri = $this->getServer("SCRIPT_NAME");
			} elseif(($this->getServer("PHP_SELF")) !== null){
				$this->_baseUri = $this->getServer("PHP_SELF");
			} 
		} else {
			$this->_baseUri = $baseUri;
		}
		

		return $this;
	}

	/**
	 * Gets a value from the Server Global
	 *
	 * @param string|null $key
	 * @param string|null $default default value to use if key cant be found
	 * @return mixed array|string|null
	 */
	public function getServer($key = null, $default = null)
    {
        if (null === $key) {
            return $_SERVER;
        }

        return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
    }

    /**
     * Returns the Method with wich the current request was made
     * 	
     * @return string {GET, POST}
     */
	public function getMethod()
	{
		return $this->getServer("REQUEST_METHOD");
	}

	/**
	 * Was this request made by POST ?
	 * @return boolean
	 */
	public function isPost()
	{
		return ($this->getMethod() === 'POST');
	}

	/**
	 * Was thos request made by GET ?
	 * @return boolean 
	 */
	public function isGet()
	{
		return ($this->getMethod() === 'GET');
	}

	/**
	 * Retrieve a member of the $_POST Superglobal.
	 * If no key is given, returns the entire $_POST array
	 * @param  string|null $key
	 * @param  string|null $default default value to use if the key cant be found
	 * @return string|array|null
	 */
	public function getPost($key = null, $default = null)
	{
		if($key === null){
			return $_POST;
		}

		return (isset($_POST[$key])) ? $_POST[$key] : $default;
	}

	/**
	 * Retrieve a member of the $_COOKIE Superglobal.
	 * If no key is given, returns the entire $_COOKIE array.
	 * @param  string|null $key
	 * @param  string|null $default default value to use if the key cant be found
	 * @return string|array|null
	 */
	public function getCookie($key = null, $default = null)
	{
		if($key === null){
			return $_COOKIE;
		}

		return (isset($_COOKIE[$key])) ? $_COOKIE[$key] : $default;
	}

	
}