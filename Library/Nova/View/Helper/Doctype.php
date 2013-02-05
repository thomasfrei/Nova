<?php
/**
 * Nova - PHP 5 Framework
 *
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2012 Thomas Frei
 * @link        https://github.com/thomasfrei/nova
 * @license 	https://github.com/thomasfrei/nova/blob/master/License.txt 
 * @package     Nova\View
 * @version     0.0.1 
 */

Namespace Nova\View\Helper;

/**
 * Doctype Helper
 * 
 * @package 		Nova\View
 * @subpackage 		Helper
 */
class Doctype {

	/**
	 * Doctype Constants
	 * @var const
	 */
	const XHTML11             = 'XHTML11';
    const XHTML1_STRICT       = 'XHTML1_STRICT';
    const XHTML1_TRANSITIONAL = 'XHTML1_TRANSITIONAL';
    const XHTML1_FRAMESET     = 'XHTML1_FRAMESET';
    const XHTML1_RDFA         = 'XHTML1_RDFA';
    const XHTML_BASIC1        = 'XHTML_BASIC1';
    const XHTML5              = 'XHTML5';
    const HTML4_STRICT        = 'HTML4_STRICT';
    const HTML4_LOOSE         = 'HTML4_LOOSE';
    const HTML4_FRAMESET      = 'HTML4_FRAMESET';
    const HTML5               = 'HTML5';
    const CUSTOM_XHTML        = 'CUSTOM_XHTML';
    const CUSTOM              = 'CUSTOM';

    /**
     * Default Doctype tp use
     * @var const
     */
    protected $_defaultDoctype = self::XHTML1_STRICT;

    /**
     * Array of Doctypes
     * @var  array
     */
    protected $_doctypes = array();

    /**
     * Doctype
     * @var  string
     */
    protected $_doctype = null;

    /**
     * Constructor
     */
    public function __construct()
    {
    	$this->_doctypes = array(
					self::XHTML11             => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
                    self::XHTML1_STRICT       => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
                    self::XHTML1_TRANSITIONAL => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
                    self::XHTML1_FRAMESET     => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
                    self::XHTML1_RDFA         => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">',
                    self::XHTML_BASIC1        => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.0//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd">',
                    self::XHTML5              => '<!DOCTYPE html>',
                    self::HTML4_STRICT        => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
                    self::HTML4_LOOSE         => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
                    self::HTML4_FRAMESET      => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
                    self::HTML5               => '<!DOCTYPE html>',
    		);
    }
   	
   	/**
   	 * Set the Doctype
   	 * 
   	 * @param  string $doctype 
   	 * @return Doctype
   	 */
	public function doctype($doctype = null)
	{
		if($this->_doctype !== null){
			return $this->getDoctype();
		}

		if($doctype !== null){
			$doctype = strtoupper($doctype);
			switch($doctype){
				case self::XHTML11:
				case self::XHTML1_STRICT:
				case self::XHTML1_TRANSITIONAL:
				case self::XHTML1_FRAMESET:
				case self::XHTML1_RDFA:
				case self::XHTML_BASIC1:
				case self::XHTML5:
				case self::HTML4_STRICT:
				case self::HTML4_LOOSE:
				case self::HTML4_FRAMESET:
				case self::HTML5 :
					$this->setDoctype($doctype);
					break;
			}
		} else {
			$this->setDoctype($this->_defaultDoctype);
		}
		return $this->_doctype;
	}

	/**
	 * Sets the Doctype
	 * 
	 * @param string $doctype
	 */
	public function setDoctype($doctype)
	{
		$this->_doctype = $this->_doctypes[$doctype];
		return $this;
	}

	/**
	 * Returns the Doctype
	 * 
	 * @return string 
	 */
	public function getDoctype()
	{
		return $this->_doctype;
	}

	/**
	 * __toString
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->getDoctype();
	}
}