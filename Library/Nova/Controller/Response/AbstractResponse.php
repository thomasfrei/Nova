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

namespace Nova\Controller\Response;

/**
 * The Base Response Object
 * 
 * @package 		Nova\Controller
 * @subpackage 		Response
 */
Abstract Class AbstractResponse
{
	/**
	 * Exceptions
	 * @var array
	 */
	protected $_exceptions = array();

	/**
	 * render Exceptions Flag, flase by default
	 * @var boolean
	 */
	protected $_renderExceptions = false;

	/**
	 * Array of headers
	 * @var array
	 */
	protected $_headers = array();

	/**
	 * Http response code to send. Default 200
	 * @var int
	 */
	protected $_httpResponseCode = 200;

	/**
	 * Body Content
	 * @var array
	 */
	protected $_body = array();

	/**
	 * Normalise a header string
	 * 
	 * input format = http-accept-encoding or http accept encoding
	 * returns format = HTTP_ACCEPT_ENCODING
	 *
	 * @param string $headerName
	 * @return string $formated
	 */
	protected function _normalizeHeader($headerName)
	{
		$formated = str_replace(array('-', '_'), ' ', (string) $headerName);
		$formated = ucwords(strtolower($formated));
		$formated = str_replace(' ', '-', $formated);
		return $formated;
	}

	/**
	 * Set the Http Response Code TO USE WITH TH HEADERS
	 *
	 * @param int $code
	 * @return Nova/Controller/Response/Abstract
	 */
	public function setHttpResponseCode($code)
	{
		/**
		 * @todo Range checking and Exceptions
		 */
		$this->_httpResponseCode = $code;
		return $this;
	}

	/**
	 * Retrieve the Http Response Code
	 * 
	 * @return int
	 */
	public function getHttpResponseCode()
	{
		return $this->_httpResponseCode;
	}

	/**
	 * Set the Headers
	 *
	 * @param string $name
	 * @param string $value
	 * @param boolean 
	 * @return Nova\Controller\Response\Abstract
	 */
	public function setHeader($name, $value, $replace = false)
	{
		$name = $this->_normalizeHeader($name);
		$value = (string) $value;

		if($replace == true) {
			foreach($this->_headers as $key => $header){
				if($header['name'] == $name){
					unset($this->_headers[$key]);
				}
			}
		}

		$this->_headers[] = array(
			'name' 		=> 	$name,
			'value'		=>	$value,
			'replace'	=>	$replace
		);

		return $this;
	}

	/**
	 * Retrieve the array of headers
	 *
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->_headers;
	}

    /**
     * Send all headers and the HttpResponseCode
     *
     * @return 
     */
    public function sendHeaders()
    {
    	$codeSent = false;

    	foreach($this->_headers as $header){
    		if(!$codeSent && $this->_httpResponseCode){
    			header($header['name'] . ':' . $header['value'], $header['replace'], $this->_httpResponseCode);
    			$codeSent = true;
    		} else {
    			header($header['name'] . ':' . $header['value'], $header['replace']);
    		}
    	}

    	if(!$codeSent){
    		header('HTTP/1.1 ' . $this->_httpResponseCode);
    		$codeSent = true;
    	}

    	return $this;
    }

	/**
	 * Set Redirect Url
	 *
	 * Sets a redirect Url and response code ({@link http://en.wikipedia.org/wiki/List_of_HTTP_status_codes})
	 * Most common reponse code are:
	 * 
	 * * 200 - OK          			- The request has succeeded. The meaning of a success varies depending on the HTTP method
	 *  								(The methods PUT, DELETE, and OPTIONS can never result in a 200 OK response.)
	 *  								
	 * * 301 - Moved Permanently		- This response code means that URI of requested resource has been changed. Probably, 
	 *  								new URI would be given in the response.
	 *  								
	 * * 307 - Temporary Redirect    - Server sent this response to directing client to get requested resource to another 
	 *  								URI with same method that used prior request
	 *  								
	 * * 308 - Permanent Redirect    - This means that the resource is now permanently located at another URI, 
	 *									specified by the Location: HTTP Response header. 
	 *									
	 * * 400 - Bad Request 			- This response means that server could not understand the request due to invalid syntax.
	 *	
	 * * 401 - Unauthorized 			- Authentication is needed to get requested response. 
	 *  								This is similar to 403, but in this case, authentication is possible.
	 *  								
	 * * 403 - Forbidden				- Client does not have access rights to the content so server is rejecting to give proper response.
	 *  
	 * * 404 - Not Found 			- erver can not find requested resource. This response code probably is most famous one due to its frequency to occur in web.
	 *  
	 * * 503 - Service Unavailable   - The server is not ready to handle the request. Common causes are a server that is down for maintenance 
	 *  								or that is overloaded. Note that together with this response, a user-friendly page explaining 
	 *  								the problem should be sent. This responses should be used for temporary conditions 
	 *  								and the Retry-After: HTTP header should, if possible, contain the estimated time before 
	 *  								the recovery of the service. The webmaster must also take care about the caching-related 
	 *  								headers that are sent along with this response, as these temporary condition responses should 
	 *  								usually not be cached.
	 *
	 * 
	 * @param [type]  $url  Url to redirect to
	 * @param integer $code Http response code
	 */	
	public function setRedirect($url, $code = 302)
	{
		$this->setHeader('Location', $url, true)
			 ->setHttpResponseCode($code);

		return $this;
	}

	/**
	 * Clear all headers
	 *
	 * @return AbstractReponse
	 */
	public function clearHeaders()
	{
		$this->_headers = array();
	}

	/**
	 * Clear a single header
	 *
	 * @param string $name
	 * @return AbstractResponse
	 */
	public function clearHeader($name)
	{
		if(!count($this->_headers)){
			return $this;
		}

		$name = $this->_normalizeHeader($name);

		foreach ($this->_headers as $index => $header) {
			if($header['name'] == $name){
				unset($this->_headers[$index]);
			}
		}

		return $this;
	}

	/**
	 * Set the body Content
	 *
	 * @param string|array $content
	 * @param string $name
	 * @return AbstractResponse
	 */
	public function setBody($content, $name = null)
	{
		if($name === null || !is_string($name)){
			$this->_body = array('default' => $content);
		} else {
			$this->_body[$name] = (string) $content;
		}

		return $this;
	}

	/**
	 * Append Content to the body array
	 *
	 * @param string $content
	 * @param null|string $name
	 * @return AbstractResponse
	 */
	public function appendBody($content, $name = null)
	{
		if($name === null || !is_string($name)){
			if (isset($this->_body['default'])){
				$this->_body['default'] .= (string) $content;
			} else {
				return $this->append('default', $content);
			}
		} elseif (isset($this->_body[$name])){
			$this->_body[$name] .= (string) $content;
		} else {
			return $this->append($name, $content);
		}

		return $this;
	}

	/**
	 * Returns the body content.
	 *
	 * If spec is false return concetenated values of body array.
	 * If spec is boolean true, returns the body array.
	 * If spec is a string and matches a segment in the body array, returns
	 * that segment otherwise null
     *
     * @param boolean $spec
     * @return string|array|null
     */
    public function getBody($spec = false)
    {
        if (false === $spec) {
            ob_start();
            $this->outputBody();
            return ob_get_clean();
        } elseif (true === $spec) {
            return $this->_body;
        } elseif (is_string($spec) && isset($this->_body[$spec])) {
            return $this->_body[$spec];
        }

        return null;
    }	

	/**
	 * Append a named Segment into the body array
	 *
	 * If the segment aleady exists, replaces it and puts it at the end of the array
	 *
	 * @param string $name
	 * @param string $content
	 * @return AbstractResponse
	 */
	public function append($name, $content)
	{
		$name = (string) $name;

		if (isset($this->_body[$name])){
			unset($this->_body[$name]);
		}

		$this->_body[$name] = (string) $content;
		return $this;
	}

	/**
	 * Prepend a named Segment into the body array
	 *
	 * If the segment aleady exists, replaces it and puts it at the beginning of the array
	 *
	 * @param string $name
	 * @param string $content
	 * @return AbstractResponse
	 */
	public function prepend($name, $content)
	{
		$name = (string) $name;

		if (isset($this->_body[$name])){
			unset($this->_body[$name]);
		}

		$newArray = array($name => (string) $content);
		$this->_body = $newArray + $this->_body;

		return $this;
	}

    /**
     * Echo the body segments
     *
     * @return void
     */
    public function outputBody()
    {
        $body = implode('', $this->_body);
        echo $body;
    }

	/**
	 * Register an Exception in the Response
	 *
	 * @param \Exception $e
	 * @return AbstractResponse
	 */
	public function setException(\Exception $e)
	{
		$this->_exceptions[] = $e;
		return $this;
	}

	/**
	 * Retrieve all exceptions registered
	 *
	 * @return array
	 */
	public function getException()
	{
		return $this->_exceptions;
	}

	/**
	 * Was an Exception Registered with the response ?
	 *
	 * @return boolean
	 */
	public function hasException()
	{
		return !empty($this->_exceptions);
	}

	/**
	 * Remove the first Exception from the Exceptions array
	 * 
	 * @return AbstractResponse
	 */
	public function removeFirstException()
	{
		if($this->hasException()){
			array_shift($this->_exceptions);
		}

		return $this;
	}

	/**
	 * Whether or not to render eventual Exceptions in the Response
	 *
	 * @param boolean
	 * @return boolean
	 */
	public function renderExceptions($flag = null)
	{
		if($flag !== null){
			return $this->_renderExceptions = $flag ? true : false;
		}

		return $this->_renderExceptions;
	}

	/**
	 * Send the Response
	 * 
	 * @return void
	 */
	public function sendResponse()
	{
		$this->sendHeaders();

		if($this->hasException() && $this->renderExceptions()){
			$exceptions = '';
			foreach($this->_exceptions as $e){
				$exceptions .= $e->__toString() . "\n";
			}
			echo $exceptions;
			return;
		}

		$this->outputBody();

	}

    /**
     * Magic __toString functionality
     *
     * Proxies to {@link sendResponse()} and returns response value as string
     * using output buffering.
     *
     * @return string
     */
    public function __toString()
    {
        ob_start();
        $this->sendResponse();
        return ob_get_clean();
    }



}