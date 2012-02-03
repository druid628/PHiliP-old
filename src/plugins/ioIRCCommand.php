<?php

/**
 * Base interface for implementing IRC commands and listeners.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */
abstract class ioIRCCommand {
    protected $_pattern;
    protected $_command;
    protected $_captures;
    protected $_description;

    /**
     * Tests the given line to see if it matches the command's pattern
     *
     * @param string $line The message sent to IRC to test for a command
     * 
     * @return boolean True if the command matches, false otherwise
     */
    public function test($line) {
        return (bool) preg_match($this->_pattern, $line);
    }

    /**
     * Returns the response that should be printed into IRC
     *
     * @param array $data The IRC message, split into parts
     *
     * @return string The message to print back into the room or FALSE to print nothing
     */
    public abstract function handle($data);

    /**
     * Runs the message through the captures regex and returns the matches.
     *
     * @param string $line The line to match against.
     */
    public function parse($line) {
        $this->_matches = array();
        preg_match($this->_captures, $line, $this->_matches);
    }

    /**
     * Just returns the command's description
     *
     * @return string The description given when the objects was created.
     */
    public function __toString() {
        return $this->_description;
    }
} 
