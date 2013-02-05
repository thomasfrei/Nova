<?php
/**
 * Nova - PHP 5 Framework
 *
 * @package     Loader
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */

Namespace Nova\Loader;

use Nova\Loader\Autoloader as Autoloader;

/**
 * Fully PSR-0 Compatible Autoloader
 *
 * @package     Loader
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2013 Thomas Frei
 * @license     https://github.com/thomasfrei/Nova/blob/master/License.txt 
 * @link        https://github.com/thomasfrei/Nova
 */
Class Autoloader
{
    /**
     * The Default File Extension
     * @var string
     */
    protected $_fileExtension = '.php';

    /**
     * The Default Namespace Separator
     * @var string
     */
    protected $_namespaceSeparator = '\\';

    /**
     * Array of Registered Namespaces
     * @var array
     */
    protected $_namespaces = array();

    /**
     * Constructor
     *
     * Accepts an Array of Autoloader Options
     *
     * Valid Options are:
     * 
     *     - file.extension
     *     - namespace.separator
     *     - include.path
     * 
     * @param null|array $options array of options
     */
    public function __construct($options = null)
    {
        if ($options !== null) {
            if (array_key_exists('file.extension', $options)) {
                $this->setFileExtension($options['file.extension']);
            }

            if (array_key_exists('namespace.separator', $options)) {
                $this->setNamespaceSeparator($options['namespace.separator']);
            }

            if (array_key_exists('include.path', $options)) {
                $this->setIncludePath($options['include.path']);
            }
        }
    }

    /**
     * Returns the File Extension.
     * 
     * @return string File Extension
     */
    public function getFileExtension()
    {
        return $this->_fileExtension;
    }

    /**
     * Sets the File Extension.
     * 
     * @param string $fileExtension
     * @return Autoloader
     */
    public function setFileExtension($fileExtension)
    {
        $this->_fileExtension = (string) $fileExtension;
        return $this;
    }

    /**
     * Returns the Namespace Separator.
     * 
     * @return string Namespace Separator
     */
    public function getNamespaceSeparator()
    {
        return $this->_namespaceSeparator;
    }

    /**
     * Sets the Namepace Separator.
     * 
     * @param string $namespaceSeparator
     * @return Autoloader
     */
    public function setNamespaceSeparator($namespaceSeparator)
    {
        $this->_namespaceSeparator = (string) $namespaceSeparator;
        return $this;
    }

    /**
     * Sets the Include Path. Does not Overwrite Previously set Paths.
     * 
     * @param array|string $newIncludePath Sting or Array with the Include Path
     * @return Autoloader
     */
    public function setIncludePath($newIncludePath)
    {
        // Grab the Original Include Path
        $originalPath = explode(PATH_SEPARATOR, $this->getIncludePath());

        // Merge the Original and the New Include Path
        if (is_array($newIncludePath)) {
            $newIncludePath = array_merge($originalPath, $newIncludePath);
        } elseif (is_string($newIncludePath)) {
            array_push($originalPath, $newIncludePath);
            $newIncludePath = $originalPath;
        }

        $newIncludePath = implode(PATH_SEPARATOR, $newIncludePath);
        unset($originalPath);

        // Set the New Include Path
        set_include_path($newIncludePath);

        return $this;
    }

    /**
     * Returns the Include Path.
     * 
     * @return string Include Path
     */
    public function getIncludePath()
    {
        return get_include_path();
    }

    /**
     * Registers the Autoloader with spl_autoload.
     * 
     * @param  boolean $throw   Whether spl_autoload_register() should throw exceptions when the autoload_function cannot be registered.
     * @param  boolean $prepend If true, spl_autoload_register() will prepend the autoloader on the autoload stack instead of appending it.
     * @return void
     */
    public function register($throw = false, $prepend = false)
    {
        spl_autoload_register(array($this, 'loadClass'), $throw, $prepend);
    }

    /**
     * Removes the Autoloader from the spl_autoload Stack.
     * 
     * @return void
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    /**
     * Register a new namespace.
     *
     * Accepts an Array of namespace=>path.
     * 
     * <code>
     *     $namespaces = array(
     *          'One' => 'path/to/namespace/one',
     *          'Two' => 'path/to/namespace/one/two',
     *      );
     *
     *      $loader->registerNamespaces($namespaces); 
     * </code>
     * 
     * @param  array $namespaces array containing namespace => path
     * @throws Exception If Namespace is Already Registered with Different Value
     * @return Autoloader
     */
    public function registerNamespaces($namespaces)
    {
        foreach ($namespaces as $namespace => $path) {
            if (!array_key_exists($namespace, $this->_namespaces)) {
                $this->_namespaces[$namespace] = $path;
            } elseif (array_key_exists($namespace, $this->_namespaces) && $this->_namespaces[$namespace] !== $path) {
                throw new Exception('Namespace: '.$namespace.' already registered with different value');
            }
        }
        return $this;
    }

    /**
     * Returns an array of registered namespaces.
     * 
     * @return array array of registered namespaces
     */
    public function getRegisteredNamespaces()
    {
        return $this->_namespaces;
    }

    /**
     * Unregister a namespace.
     * 
     * @param  string $namespace name of the namespace to unregister
     * @return Autoloader
     */
    public function unregisterNamespace($namespace)
    {
        if (isset($this->_namespaces[$namespace])) {
            unset($this->_namespaces[$namespace]);
        }

        return $this;
    }

    /**
     * Autoload a Class.
     * 
     * @param  string $classname Name of the class
     * @throws Exception If The File Cannot be Found
     * @return boolean true if the file could be included
     */
    protected function loadClass($classname)
    {
        // check if the file is already loaded
        if ($this->isLoaded($classname)) {
            return true;
        }

        // Transform classname
        $filename = $this->transformClassnameToFilename($classname);

        // Search the file
        $file = $this->findFile($filename);

        // The file was not found
        if (!$file) {
            throw new Exception('File '. $filename .' could not be loaded');
        }

        // include the file
        require_once($file);

        return true;
    }

    /**
     * Checks if the class is already loaded.
     * 
     * @param  string  $classname Name of the class
     * @return boolean 
     */
    protected function isLoaded($classname)
    {
        if (!class_exists($classname, false)) {
            return false;
        }
        return true;
    }

    /**
     * Search the file against the registered namespaces aand the include path.
     * 
     * @param  string $filename Name of the file
     * @return boolean|string $filePath False If the file can't be found or the full path to the File
     */
    protected function findFile($filename)
    {
        $filePath = '';

        // Search with registered namespaces first
        foreach ($this->_namespaces as $namespace => $path) {
            if ($found = (strripos($filename, $namespace) === 0)) {

                $class = substr($filename, strlen($namespace));
                $class = ltrim($class, DIRECTORY_SEPARATOR);
                $filePath = $this->_namespaces[$namespace] . $class;
            }

            // Check if the file exists
            if (file_exists($filePath)) {
                return $filePath;
            }
        }

        // If not found try the include path
        if ($filePath = stream_resolve_include_path($filename)) {
            return $filePath;
        }

        // The file doesn't exist
        return false;
    }

    /**
     * Transforms Classnames to Filenames by PSR-0 Standards.
     * 
     * @link  https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
     * @param  string $classname Name of the Class 
     * @return string $filename Name of the File
     */
    protected function transformClassnameToFilename($classname)
    {
        // Transform Classnames according to PSR-0
        $classname     = ltrim($classname, $this->_namespaceSeparator);
        $filename      = '';
        $namespace = '';

        // Find the classnamename
        if ($last_namespace_position = strripos($classname, $this->_namespaceSeparator)) {
            $namespace = substr($classname, 0, $last_namespace_position);
            $classname = substr($classname, $last_namespace_position + 1);
            $classname = str_replace('_', DIRECTORY_SEPARATOR, $classname);
            $filename = str_replace($this->_namespaceSeparator, DIRECTORY_SEPARATOR, $namespace).DIRECTORY_SEPARATOR;
        }

        // Underscores in classnames must be converted to Directory Separators
        $filename .= str_replace('_', DIRECTORY_SEPARATOR, $classname) . $this->_fileExtension;

        return $filename;
    }
}