<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     Http
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace Nova\Http;

use Nova\Http\AbstractRequest as AbstractRequest;

/**
 * Request
 *
 * @package     Http
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 * @todo        Implement isPUT()
 * @todo        Implement isDELETE()
 * @todo        Implement isHEAD()
 * @todo        Implement isOPTIONS()
 * @todo        Implement isXmlHttpRequest()
 * @todo        Implement GETcOOKIE()
 */
Class Request extends AbstractRequest
{
    /**
     * The Request Uri
     * @var string
     */
    protected $_requestUri = null;

    /**
     * The Base Uri
     * @var string
     */
    protected $_baseUri;

    /**
     * Contructor
     *
     * Automatically calls the setRequestUri and setBaseUri methods
     *
     * @param string $requestUri
     * @return void
     */
    public function __construct($requestUri = null)
    {
        $this->setRequestUri($requestUri)->setBaseUri();
    }

    /**
     * Sets the Request Uri
     *
     * @param  mixed 
     * @return AbstractRequest
     */
    public function setRequestUri($requestUri = null)
    {
        if ($requestUri !== null) {
            $this->_requestUri = $requestUri;
        } else {
            if (isset($_SERVER["REQUEST_URI"])) {
                $this->_requestUri = $_SERVER["REQUEST_URI"];
            }
        }

        return $this;
    }

    /**
     * Returns the Request Uri.
     * 
     * @return string The Request Uri
     */
    public function getRequestUri()
    {
        return $this->_requestUri;
    }

    /**
     * Returns the base Uri
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
        if ($baseUri === null) {
            if (($this->getServer("SCRIPT_NAME")) !== null) {
                $this->_baseUri = $this->getServer("SCRIPT_NAME");
            } elseif (($this->getServer("PHP_SELF")) !== null) {
                $this->_baseUri = $this->getServer("PHP_SELF");
            }
        } else {
            $this->_baseUri = $baseUri;
        }

        return $this;
    }

    /**
     * Returns a Value Fron the $_SERVER global
     *
     * Returns Entire $_SERVER Array if Key has a value of null
     * 
     * @param  mixed  $key     Key or null
     * @param  mixed  $default Default Value to Return if Key is not found
     * @return mixed  
     */
    public function getServer($key = null, $default = null)
    {
        if (null === $key) {
            return $_SERVER;
        }

        return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
    }

    /**
     * Retrieve a Value of the $_POST Superglobal.
     * 
     * Returns Entire $_SERVER Array if Key has a Value of null.
     * 
     * @param  mixed $key
     * @param  mixed $default Default Value to Return if Key is not found
     * @return mixed
     */
    public function getPost($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }

        return (isset($_POST[$key])) ? $_POST[$key] : $default;
    }

    /**
     * Returns The Request Method
     * @return string Request Method
     */
    public function getMethod()
    {
        return $this->getServer('REQUEST_METHOD');
    }

    /**
     * Is this a POST Request ?
     * @return boolean 
     */
    public function isPost()
    {
        return ($this->getMethod() === 'POST');
    }

    /**
     * Is this a GET Request ?
     * @return boolean
     */
    public function isGet()
    {
        return ($this->getMethod() === 'GET');
    }
}