<?php

/**
 * A base class for "listening" commands (commands that respond to normal channel
 * chit-chat, rather than a specific command).
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */
abstract class ioBaseIRCListener extends ioIRCCommand {
    protected $_pattern;
    protected $_captures;
    protected $_description;

    /**
     * Takes the command pattern and a description of the command
     *
     * @param regex  $pattern     The pattern used to match against the IRC message
     * @param string $description A short description of the command.
     */
    public function __construct($pattern = '', $description = '') {
        $this->_pattern = $pattern;
        $this->_description = $description;
    }

    /**
     * @see ioIRCCommand#parse()
     */
    public function parse($line) {
        $matches = array();
        preg_match('/' . $this->_captures . '/', $line, $matches);
        return $matches;
    }
}

