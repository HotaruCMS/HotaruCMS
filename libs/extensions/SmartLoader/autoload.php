<?php
 
/**
 * SplClassLoader implementation that implements the technical interoperability
 * standards for PHP 5.3 namespaces and class names.
 *
 * http://groups.google.com/group/php-standards/web/final-proposal
 *
 *     // Example which loads classes for the Doctrine Common package in the
 *     // Doctrine\Common namespace.
 *     $classLoader = new SplClassLoader('Doctrine\Common', '/path/to/doctrine');
 *     $classLoader->register();
 *
 * @author Jonathan H. Wage <jonwage@gmail.com>
 * @author Roman S. Borschel <roman@code-factory.org>
 * @author Matthew Weier O'Phinney <matthew@zend.com>
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Fabien Potencier <fabien.potencier@symfony-project.org>
 * @author Lissachenko Alexander <lisachenko.it@gmail.com>
 */
class SplClassLoader
{
    private $_fileExtension = '.php';
    private $_namespace;
    private $_includePaths;
    private $_namespaceSeparator = '\\';
 
    /**
     * Creates a new <tt>SplClassLoader</tt> that loads classes of the
     * specified namespace.
     *
     * @param string              $ns The namespace to use.
     * @param string|null|array   $includePath One or more include paths to use
     */
    public function __construct($ns = null, $includePath = null)
    {
        $this->_namespace = $ns;
        $this->_includePaths = (array) $includePath;
    }
 
    /**
     * Sets the namespace separator used by classes in the namespace of this class loader.
     * 
     * @param string $sep The separator to use.
     */
    public function setNamespaceSeparator($sep)
    {
        $this->_namespaceSeparator = $sep;
    }
 
    /**
     * Gets the namespace seperator used by classes in the namespace of this class loader.
     *
     * @return string
     */
    public function getNamespaceSeparator()
    {
        return $this->_namespaceSeparator;
    }
 
    /**
     * Sets the base include path for all class files in the namespace of this class loader.
     * 
     * @param string|array $includePath One or more include paths
     */
    public function setIncludePath($includePath)
    {
        $this->_includePaths = (array) $includePath;
    }
 
    /**
     * Gets the base include path for all class files in the namespace of this class loader.
     *
     * @return string|array
     */
    public function getIncludePath()
    {
        return count($this->_includePaths) > 1 ? $this->_includePaths : reset($this->_includePaths);
    }
 
    /**
     * Sets the file extension of class files in the namespace of this class loader.
     * 
     * @param string $fileExtension
     */
    public function setFileExtension($fileExtension)
    {
        $this->_fileExtension = $fileExtension;
    }
 
    /**
     * Gets the file extension of class files in the namespace of this class loader.
     *
     * @return string $fileExtension
     */
    public function getFileExtension()
    {
        return $this->_fileExtension;
    }
 
    /**
     * Installs this class loader on the SPL autoload stack.
     * 
     * @return bool
     */
    public function register()
    {
        return spl_autoload_register(array($this, 'loadClass'));
    }
 
    /**
     * Uninstalls this class loader from the SPL autoloader stack.
     *
     * @return bool
     */
    public function unregister()
    {
        return spl_autoload_unregister(array($this, 'loadClass'));
    }
 
    /**
     * Loads the given class or interface.
     *
     * @param string $className The name of the class to load.
     *
     * @return bool Success status
     */
    public function loadClass($className)
    {
        $isFound = false;
        
        if (null === $this->_namespace || $this->_namespace.$this->_namespaceSeparator === substr($className, 0, strlen($this->_namespace.$this->_namespaceSeparator))) {
            $fileName  = '';
            $namespace = '';
//            if (false !== ($lastNsPos = strripos($className, $this->_namespaceSeparator))) {
//                $namespace = substr($className, 0, $lastNsPos);
//                $className = substr($className, $lastNsPos + 1);
//                $fileName  = str_replace($this->_namespaceSeparator, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
//            }
            //$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . $this->_fileExtension;
            
            $fileName = $className . $this->_fileExtension;
            print $fileName . '<br/>***********<br/>';
            $includePaths = $this->_includePaths ?: array('.');
            foreach ($includePaths as $includePath) {
                
                $unresolvedFilePath = $includePath . DIRECTORY_SEPARATOR . $fileName;
                print "checking in : " . $unresolvedFilePath;
                $isFound = $this->tryLoadClassByPath($className, $unresolvedFilePath);
                if ($isFound) {
                    print "    FOUND<br/><br/>";
                    break;
                }
                print '<br/>';
            }           
        }       
        print "<br/>";
        return $isFound;
    }
 
    /**
     * Tries to load class by path
     *
     * @param string $className Name of the class to load
     * @param string $unresolvedFilePath Absolute or relative path to the file
     *
     * @return bool Success status
     */
    private function tryLoadClassByPath($className, $unresolvedFilePath)
    {
        $filePath = stream_resolve_include_path($unresolvedFilePath);
        $isFound  = false !== $filePath;
        if ($isFound) {
            require $filePath;
            $isFound = class_exists($className, false);
        }
        return $isFound;        
    }
}

?>