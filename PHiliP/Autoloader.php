<?php

namespace PHiliP;

/**
 * A basic, PSR-0 compliant Autoloader.
 *
 * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */
class Autoloader {

    /**
     * A list of paths to look for files in.
     * 
     * @var array
     * @access private
     */
    private $_paths = array();


    /**
     * Constructor.
     * 
     * @param array $paths The paths to initialize the autoloader with 
     */
    public function __construct($paths = array()) {
        if (!empty($paths)) {
            $this->addPaths($paths);
        }

    }


    /**
     * Adds a paths to the list of paths to check. 
     * 
     * @param string $path The path to add
     */
    public function addPath($path = '') {
        if (!empty($path)) {
            $this->addPaths(array($path));
        }
    }


    /**
     * Adds multiple paths to the list of paths to check.
     * 
     * @param array $paths The paths to add
     */
    public function addPaths($paths = array()) {
        $this->_paths = array_unique(array_merge($this->_paths, $paths));
    }


    /**
     * Loops through the known paths and requres the given class. 
     * 
     * @param string $className The classname, with namespace, to load
     */
    private function load($className) {
        foreach ($this->_paths as $path) {
            $className = ltrim($className, '\\');
            $fileName  = '';
            $namespace = '';
            if ($lastNsPos = strripos($className, '\\')) {
                $namespace = substr($className, 0, $lastNsPos);
                $className = substr($className, $lastNsPos + 1);
                $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }
            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

            $fullPath = $path . '/' . $fileName;
            if (file_exists($fullPath)) {
                require_once $fullPath;
                return true;
            }
        }

        return false;
    }


    /**
     * Registers the autoloader. 
     *
     * @see http://php.net/manual/en/function.spl-autoload-register.php
     */
    public function register() {
        spl_autoload_register(array($this, 'load'), true, true);
    }
}
