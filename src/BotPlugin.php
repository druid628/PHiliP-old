<?php

/**
 * Base interface for implementing IRC commands and listeners.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */

namespace PHiliP;

abstract class BotPlugin {

    /** @var string $_pattern The regular expression to test against. */
    protected $_pattern;

    /** @var string $_command The command name. */
    protected $_command;

    /** @var string $_captures The regular expression that captures parameters. */
    protected $_captures;

    /** @var string $_description A description of the plugin. */
    protected $_description;


    /**
     * Initializes the plugin.
     *
     * @param array $options The options to use when initializing
     */
    public function init($options = array()) {
        // Plugins should only override this if they have initialization to do.
    }


    /**
     * Tests the given line to see if it matches the command's pattern
     *
     * @param string $line The message sent to IRC to test for a command
     * 
     * @return bool True if the command matches, false otherwise
     */
    public function test($line) {
        return (bool) preg_match($this->_pattern, $line);
    }


    /**
     * Returns the response that should be printed into IRC
     *
     * @param array $req    The IRC request object
     * @param array $matches The array of things that match the plugin-defined captures
     *
     * @return Response The IRC Response object to be sent back to the server
     */
    public abstract function handle($req, $matches);


    /**
     * Runs the message through the captures regex and returns the matches.
     *
     * @param string $line The line to match against.
     */
    public abstract function parse($line);


    /**
     * Just returns the command's description
     *
     * @return string The description given when the objects was created.
     */
    public function __toString() {
        return $this->_description;
    }
} 
