<?php
/**
 * Nova - PHP 5 Framework
 *
 * @author      Thomas Frei <thomast.frei@gmail.com>
 * @copyright   2012 Thomas Frei
 * @link        https://github.com/thomasfrei/nova
 * @license     https://github.com/thomasfrei/nova/blob/master/License.txt 
 * @package     Nova\Loader
 * @version     0.0.1 
 */

namespace Nova\Loader;

/**
 * Autoloader
 *
 * PSR-0 Compatible Autoloader
 *
 * @package  Nova\Loader
 * @subpackage Autoloader
 */
Class Autoloader
{
    /**
     * The extension of the files to load
     * @var string
     */
    protected $fileExtension = '.php';

    /**
     * Namespace separator
     * @var string
     */
    protected $namespaceSeparator = '\\';

    /**
     * An array of registered namespaces with corresponding paths
     * @var array
     */
    protected $namespaces = array();

    /**
     * Constructor
     * 
     * @param null|array $options null ur array of autoloader options
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
     * Sets the namespace separator.
     * 
     * @param string $nsSeperator The namespace separator
     * @return Autoloader
     */
    public function setNamespaceSeparator($nsSeperator)
    {
        $this->namespaceSeparator = (string) $nsSeperator;
        return $this;
    }

    /**
     * Returns the currently used namespace separator.
     * 
     * @return string The namespace separator string
     */
    public function getNamespaceSeparator()
    {
        return $this->namespaceSeparator;
    }

    /**
     * Sets the file extension the autoloader looks for when loading files.
     * 
     * @param string $fileExtension
     * @return Autoloader
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = (string) $fileExtension;
        return $this;
    }

    /**
     * Returns the file extension.
     * 
     * @return string File extension
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * Sets the include path.
     * 
     * @param array|string $newPath string or array with the include path/paths
     * @return Autoloader
     */
    public function setIncludePath($newPath)
    {
        // Get the original include path
        $originalPath = explode(PATH_SEPARATOR, get_include_path());

        // Merge the original and the new include paths
        if (is_array($newPath)) {
            $newPath = array_merge($originalPath, $newPath);
        } elseif (is_string($newPath)) {
            array_push($originalPath, $newPath);
            $newPath = $originalPath;
        }

        if (is_array($newPath)) {
            $newPath = implode(PATH_SEPARATOR, $newPath);
        }

        // Set the include path
        set_include_path($newPath);

        return $this;
    }
    
    /**
     * Returns the include path.
     * 
     * @return string The current include path
     */
    public function getIncludePath()
    {
        return get_include_path();
    }

    /**
     * Registers the autoloader with spl_autoload.
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
     * Removes the autoloader from the spl_autoload stack.
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
     * $namespaces = array(
     *      'Namespace\One' => 'path/to/namespace/one',
     *      'Namespace\Two' => 'path/to/namespace/one/two',
     * );
     *
     * $loader->registerNamespaces($namespaces); 
     * 
     * @param  array $namespaces array containing namespace => path
     * @return Autoloader
     */
    public function registerNamespaces($namespaces)
    {
        foreach ($namespaces as $namespace => $path) {
            if (!array_key_exists($namespace, $this->namespaces)) {
                $this->namespaces[$namespace] = $path;
            } elseif (array_key_exists($namespace, $this->namespaces) && $this->namespaces[$namespace] !== $path) {
                throw new Exception('Namespace: '.$namespace.' already registered with different value');
            }
        }
        return $this;
    }

    /**
     * Unregister a namespace.
     * 
     * @param  string $namespace name of the namespace to unregister
     * @return Autoloader
     */
    public function unregisterNamespace($namespace)
    {
        if (isset($this->namespaces[$namespace])) {
            unset($this->namespaces[$namespace]);
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
        return $this->namespaces;
    }

    /**
     * Transforms a class name to a filename
     * 
     * @param  string $class Classname
     * @return string $filename Filename
     */
    protected function transformClassnameToFilename($class)
    {
        // Transform Classnames according to PSR-0
        $class     = ltrim($class, $this->namespaceSeparator);
        $filename      = '';
        $namespace = '';

        // Find the classname
        if ($last_namespace_position = strripos($class, $this->namespaceSeparator)) {
            $namespace = substr($class, 0, $last_namespace_position);
            $class = substr($class, $last_namespace_position + 1);
            $class = str_replace('_', DIRECTORY_SEPARATOR, $class);
            $filename = str_replace($this->namespaceSeparator, DIRECTORY_SEPARATOR, $namespace).DIRECTORY_SEPARATOR;
        }
        $filename .= str_replace('_', DIRECTORY_SEPARATOR, $class) . $this->fileExtension;

        return $filename;
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
     * Autoload a class.
     * 
     * @param  string $classname Name of the class
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
            throw new Exception('File '. $file .' could not be loaded');
        }

        // include the file
        require_once($file);

        return true;
    }

    /**
     * Search the file against the registered namespaces aand the include path.
     * 
     * @param  string $filename Name of the file
     * @return string|boolean $filePath false or Path to the file
     */
    protected function findFile($filename)
    {
        // Search with registered namespaces first
        foreach ($this->namespaces as $namespace => $path) {
            if ($found = (strripos($filename, $namespace) === 0)) {
                $class = substr($filename, strlen($namespace));
                $class = ltrim($class, DIRECTORY_SEPARATOR);
                $filePath = $this->namespaces[$namespace] . $class;
                
                // Check if the file exists
                if (file_exists($filePath)) {
                    return $filePath;
                }
            }
        }

        // If not found try the include path
        if ($filePath = stream_resolve_include_path($filename)) {
            return $filePath;
        }

        // The file doesn't exist
        return false;
    }
}
