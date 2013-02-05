<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     Nova\Http     
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace Nova\Http;

/**
 * AbstractResponse
 *
 * @package     Nova\Http      
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
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
     * Set the Http Response Code TO USE WITH TH HEADERS
     *
     * @param int $code
     * @return Nova/Controller/Response/Abstract
     */
    public function setHttpResponseCode($code)
    {
        if ( (!is_int($code)) || ($code < 100) || ($code > 599) ) {
            throw new Exception('Invalid Http Response Code');
        }
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
        $name = $this->normalizeHeader($name);
        $value = (string) $value;

        if($replace == true) {
            foreach($this->_headers as $key => $header){
                if($header['name'] == $name){
                    unset($this->_headers[$key]);
                }
            }
        }

        $this->_headers[] = array(
            'name'      =>  $name,
            'value'     =>  $value,
            'replace'   =>  $replace
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
     * @param string  $url  Url to redirect to
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

        $name = $this->normalizeHeader($name);

        foreach ($this->_headers as $index => $header) {
            if($header['name'] == $name){
                unset($this->_headers[$index]);
            }
        }

        return $this;
    }

    /**
     * Normalise a header string
     *
     * @param string $headerName
     * @return string $formated
     */
    public function normalizeHeader($headerName)
    {
        $formated = str_replace(array('-', '_'), ' ', (string) $headerName);
        $formated = ucwords(strtolower($formated));
        $formated = str_replace(' ', '-', $formated);
        return $formated;
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