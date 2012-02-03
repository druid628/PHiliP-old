<?php

/**
 * A base class for the plugins. A plugin should only have to call the parent
 * constructor and provide a handle() method.
 *
 * NOTE: You can see an example plugin in FirePeople.php.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */
abstract class ioBaseIRCBotCommand extends ioIRCCommand {

    /**
     * Takes the command pattern and a description of the command
     *
     * @param regex  $pattern     The pattern used to match against the IRC message
     * @param string $description A short description of the command.
     */
    public function __construct($command = '', $captures = '', $description = '') {
        $this->_pattern = "/^:!$command\s+$captures/";
        $this->_command = $command;
        $this->_captures = $captures;
        $this->_description = $description;
    }

    /**
     * @see ioIRCCommand#handle()
     */
    public abstract function handle($data);


}
