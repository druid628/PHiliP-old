<?php
/**
 * A basic autoloader. This autoloader expects classes to be named the same
 * as the file that contains them.
 *
 * @author Bill Israel <bill.israel@iostudio.com>
 */

namespace PHiliP;

use \Exception;

class Autoloader {
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
     * If found, it requires it. If not found, throws an exception.
     *
     * To accomodate the plugins, the autoloader will first look
     * for a directory matching the class name, if it finds one,
     * it will look in that directory for a file with the same
     * name.
     *
     * @param string $class The name of the class to find
     */
    public function load($class) {
        $full = explode('\\', $class);
        $class = $full[count($full) - 1];
        foreach($this->_paths as $path) {
            $dir  = $path . DIRECTORY_SEPARATOR . $class;

            if (is_dir($dir)) {
                $file = $dir . DIRECTORY_SEPARATOR . "$class.php";
            } else {
                $file = $path . DIRECTORY_SEPARATOR . "$class.php";
            }
        
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

