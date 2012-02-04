<?php

/**
 * A simple handler for the basic IRC commands.
 * Each IRC command should map to a function of the same name.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */

namespace PHiliP;

use PHiliP\IRCConstants;

class IRCCommandHandler {

    /** @var array $_plugins The list of known plugins */
    public $_plugins;

    /**
     * Accepts an array of plugins for handling bot commands.
     * Plugins are expected to extend from ioBaseIRCCommand.php
     *
     * @param array $plugins An array of PHiliP IRC bot commands
     */
    public function __construct($plugins = array()) {
        $this->_plugins = $plugins;
    }

    /**
     * PING command handler; just responds to PING commands with an appropriate PONG.
     *
     * @param array $parts The IRC message broken into parts
     */
    public function ping($parts) {
        return "PONG :" . $parts[IRCConstants::$IRC_MSG];
    }

    /**
     * For now, don't handle this gracefully, just panic and exit.
     *
     * @param array $parts The IRC message broken into parts
     */
    public function error($parts) {
        exit(2);
    }

    /**
     * PRIVMSG commands will loop through the list of given plugins
     * and try to find a bot command that knows how to handle a line like this.
     * 
     * @param array $parts The IRC message broken into parts
     */
    public function privmsg($parts) {
        $line = $parts[IRCConstants::$IRC_MSG];
        foreach ($this->_plugins as $plugin) {
            if ($plugin->test($line)) {
                $matches = $plugin->parse($line);
                $ret_msg = $plugin->handle($parts, array_slice($matches, 1));

                return 'PRIVMSG ' . $parts[IRCConstants::$IRC_CHAN] . " :$ret_msg";
            }
        }

        return false;
    }

    /**
     * It should be safe to ignore NOTICE messages for now, right?
     *
     * @param array $parts The IRC message broken into parts
     */
    public function notice($parts) {
        return false;
    }

    /**
     * It should be safe to ignore MODE messages for now, right?
     *
     * @param array $parts The IRC message broken into parts
     */
    public function mode($parts) {
        return false;
    }
}
