<?php
/**
 * A basic autoloader. This autoloader expects classes to be named the same
 * as the file that contains them.
 *
 * @author Bill Israel <bill.israel@iostudio.com>
 */
class ioAutoloader {
    /** @var array $paths The paths to look for classes */
    private $_paths;

    /**
     * Constructor
     */
    public function __construct($paths = array()) {
        $this->_paths = $paths;
    }

    /**
     * Adds a path to the list of searchable paths.
     */
    public function addPath($path) {
        array_push($this->_paths, $path);
    }

    /**
     * Loops through paths looking for a matching file.
     * If found, it requires it.
     * If not found, throws an exception.
     */
    public function load($class) {
        foreach($this->_paths as $path) {
            $file = $path . DIRECTORY_SEPARATOR . "$class.php";
            if (file_exists($file)) {
                require_once($file);
                return;
            }
        }

        throw new Exception("Expected class $class wasn't found.");
    }


    /**
     * The function to call to register the autoloader.
     */
    public function register() {
        spl_autoload_register(array($this, 'load'), true, true);
    }
}

