<?php

namespace PHiliP\Plugin;

/**
 * Base class for implementing IRC commands and listeners.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */

use \sfEvent;
use PHiliP\IRC\Response;

abstract class BotPlugin {
    
    /** @var string $_help_msg The plugin's help message */
    protected $_help_msg;

    /** @var string $_captures A regular expression defining "parameters" for the plugin */
    protected $_captures;

    /**
     * Registers the help message with the event listener.
     *
     * @param sfEventDispatcher $dispachter The event dispatcher
     */
    public function registerHelp($dispatcher) {
        $dispatcher->connect('bot.command.help', array($this, 'getHelpMessage'));
    }

    /**
     * Returns the help message for this plugin.
     *
     * @see PHiliP\Plugin\BotPlugin#getHelpMessage()
     */
    public function getHelpMessage(sfEvent $event) {
        $req = $event['request'];
        $event->setReturnValue(new Response('PRIVMSG', array(
            $req->getSource(),
            $this->_help_msg))
        );
    }

    /**
     * Parse the message and return the captures defined by the plugin,
     * removing the command if this message was a command.
     *
     * @param string $msg The IRC message 
     *
     * @return array The array of matches
     */
    protected function parse($msg) {
        if ($this->isCommand($msg)) {
            if (($where = strpos($msg, ' ')) !== false) {
                $msg = trim(substr($msg, $where));
            }
        }

        $matches = array();
        preg_match($this->_captures, $msg, $matches);

        return array_slice($matches, 1);
    }

    /**
     * Checks to see if the message is a command.
     *
     * @param string $msg The IRC message
     *
     * @return bool True if the message is a command, false otherwise
     */
    private function isCommand($msg) {
        return strpos($msg, '!') === 0;
    }
}
