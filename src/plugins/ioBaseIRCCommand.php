<?php

/**
 * A base class for the plugins. A plugin should only have to call the parent
 * constructor and provide a handle() method.
 *
 * NOTE: You can see an example plugin in FirePeople.php.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */
abstract class ioBaseIRCCommand {
    protected $pattern;
    protected $description;

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
     * Tests the given line to see if it matches the command's pattern
     *
     * @param string $line The message sent to IRC to test for a command
     * 
     * @return boolean True if the command matches, false otherwise
     */
    public function match($line) {
        if (strpos($line, ':') === 0) {
            $line = substr($line, 1);
        }

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
     * Just returns the command's description
     */
    public function __toString() {
        return $this->_description;
    }
}
