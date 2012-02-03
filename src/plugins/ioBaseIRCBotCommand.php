<?php

/**
 * A base class for bot commands.
 *
 * Bot commands are of the form "!<command>", and this class lets you
 * define the command and how it collects parameters (if it collects them).
 *
 * NOTE: You can see an example bot command in FirePeople.php.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */
abstract class ioBaseIRCBotCommand extends ioIRCCommand {
    protected $_pattern;
    protected $_command;
    protected $_captures;
    protected $_description;

    /**
     * Takes the command pattern and a description of the command
     *
     * @param string $command     The name of the command to match
     * @param regex  $captures    The pattern used to match against the IRC message
     * @param string $description A short description of the command.
     */
    public function __construct($command = '', $captures = '', $description = '') {
        $this->_pattern = '/^!' . $command . '\s+' . $captures . '/';
        $this->_command = $command;
        $this->_captures = $captures;
        $this->_description = $description;
    }

    /**
     * Removes the command from the line before testing it for something to capture.
     *
     * @see ioIRCCommand#parse()
     */
    public function parse($line) {
        $matches = array();
        $line = str_replace("!{$this->_command}", '', $line);
        preg_match("/{$this->_captures}/", $line, $matches);
        return $matches;
    }
}
