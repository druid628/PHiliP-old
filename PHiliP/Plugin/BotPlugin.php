<?php

namespace PHiliP\Plugin;

/**
 * Base class for implementing IRC commands and listeners.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */

use \sfEvent;

use PHiliP\Utilities;
use PHiliP\IRC\Response;

abstract class BotPlugin {
    
    /** @var string $_help_msg The plugin's help message */
    protected $_help_msg = array();

    /** @var string $_captures A regular expression defining "parameters" for the plugin */
    protected $_captures;

    /** @var bool $_registered_help Have we already registered the help handler? */
    private $_registered_help = false;

    /** @var bool $_is_command Is this plugin a command? */
    private $_is_command;

    /** @var string $_bot_command The bot command being issued */
    private $_bot_command;

    /**
     * Registers a bot command.
     *
     * @param sfEventDispatcher $dispatcher For connecting the event
     * @param string            $name       The name of the command
     * @param string            $captures   RegEx for capturing parameters
     * @param string            $help_msg   Help message for the command
     */
    public function registerCommand($dispatcher, $name, $captures, $help_msg) {
        $this->_is_command = true;
        $this->_captures = $captures;
        array_push($this->_help_msg, $help_msg);

        if (!$this->_registered_help) {
            $dispatcher->connect('bot.command.help', array($this, 'getHelpMessage'));
            $this->_registered_help = true;
        }

        $dispatcher->connect("bot.command.$name", array($this, 'handleIncoming'));
    }

    /**
     * Registers a bot listener.
     *
     * Listeners don't respond to commands, but rather listen to the idle
     * chatter in the room and respond to that.
     *
     * @param sfEventDispatcher $dispatcher For connecting the event
     * @param string            $captures   RegEx for capturing parameters
     */
    public function registerListener($dispatcher, $captures) {
        $this->_captures = $captures;
        $this->_is_command = false;
        $dispatcher->connect("server.command.privmsg", array($this, 'handleIncoming'));
    }

    /**
     * Performs the rote work of parsing the message for captures,
     * then calls down into the handle method implemented by the plugins.
     *
     * @param sfEvent $event The event to handle.
     */
    public function handleIncoming(sfEvent $event) {
        $req = $event['request'];
        $conf = $event['config'];
        $matches = $this->parse($req->getMessage());
        $res = $this->handle($req, $conf, $matches);

        if (!empty($res)) {
            $event->setReturnValue($res);
        }
    }

    /**
     * Returns the help message for this plugin.
     *
     * @see PHiliP\Plugin\BotPlugin#getHelpMessage()
     */
    public function getHelpMessage(sfEvent $event) {
        $req = $event['request'];
        $src = $req->isPrivateMessage() ? $req->getSource : $req->getSendingUser();
        $helps = array();

        foreach ($this->_help_msg as $msg) {
            array_push($helps, new Response('PRIVMSG', array(
                $src,
                $msg
            )));
        }

        $event->setReturnValue($helps);
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
        if ($this->_is_command) {
            // If there's more than just the command, strip
            // off the command and just capture against
            // the message.
            if (($where = strpos($msg, ' ')) !== false) {
                $this->_bot_command = trim(substr($msg, 1, $where));
                $msg = trim(substr($msg, $where));
            } else {
                $this->_bot_command = trim($msg, '!');
                $msg = '';
            }
        }

        $matches = array();
        preg_match($this->_captures, $msg, $matches);

        return array_slice($matches, 1);
    }


    /**
     * Returns the bot command being issued.
     *
     * @return string The command being issued
     */
    protected function getBotCommand() {
        return $this->_bot_command;
    }


    /**
     * Should be overriden by command and listeners.
     * Does the 'meaty' work of actually doing something and
     * responding to the IRC message.
     *
     * @param Request $req     The IRC Request
     * @param array   $conf    The configuration array
     * @param array   $matches The array of captured matches
     *
     * @return Response The IRC response to send back to the server
     */
    public function handle($req, $conf, $matches) {
        // implemented by commands and listeners
    }
}
