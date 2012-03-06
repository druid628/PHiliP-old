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
    protected $_help_msgs = array();

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
     * @param string|array      $names      The name(s) of the command(s)
     * @param string            $captures   RegEx for capturing parameters
     * @param string|array      $help_msgs  Help message(s) for the command(s)
     */
    protected function registerCommand($dispatcher, $names, $captures, $help_msgs) {
        $this->_is_command = true;
        $this->_captures = $captures;

        if (!is_array($names)) {
            $names = array($names);
        }

        foreach ($names as $name) {
            $dispatcher->connect("bot.command.$name", array($this, 'handleIncoming'));
        }

        $this->registerHelpMessage($dispatcher, $help_msgs);
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
    protected function registerListener($dispatcher, $captures) {
        $this->_captures = $captures;
        $this->_is_command = false;
        $dispatcher->connect("server.command.privmsg", array($this, 'handleIncoming'));
    }

    /**
     * Registers another help message with the help event.
     *
     * @param sfEventDispatcher $dispatcher The event dispatcher for registering the event
     * @param string|array      $help_msgs  The message(s) to return when the event is thrown
     */
    protected function registerHelpMessage($dispatcher, $help_msgs) {
        if(is_array($help_msgs)) {
            $this->_help_msgs = array_merge($this->_help_msgs, $help_msgs);
        } else {
            array_push($this->_help_msgs, $help_msgs);
        }

        if (!$this->_registered_help) {
            $dispatcher->connect('bot.command.help', array($this, 'handleHelp'));
            $this->_registered_help = true;
        }
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
     * Returns the help message(s) for this plugin.
     */
    public function handleHelp(sfEvent $event) {
        $req = $event['request'];
        $src = $req->isPrivateMessage() ? $req->getSource : $req->getSendingUser();
        $helps = array();

        foreach ($this->_help_msgs as $msg) {
            array_push($helps, new Response('PRIVMSG', array($src, $msg)));
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
