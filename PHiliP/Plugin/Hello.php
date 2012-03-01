<?php

namespace PHiliP\Plugin;

/**
 * A simple bot that replies when someone says hello to it.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */

use \sfEvent;

use PHiliP\Plugin\BotPlugin;
use PHiliP\IRC\Response;

class Hello extends BotPlugin {

    /** @var array $_hi_words List of words people use to say hi, usually */
    private $_hi_words = array(
        'hi', 'hello', 'hey', 'yo', 'sup', 'hallo', 'wassup', 'hiya',
    );

    /**
     * Constructor.
     */
    public function __construct($dispatcher) {
        // no need to capture anything for this one
        $this->registerListener($dispatcher, '');
    }

    /**
     * Listens for the bot's name, then tries to be helpful.
     *
     * @see BotPlugin#handle()
     */
    public function handle($req, $conf, $matches) {
        $nick = $event['config']['irc']['nick'];
        if ($this->test($req->getMessage(), $nick)) {
            return new Response('PRIVMSG', array(
                $req->getSource(),
                "Hi, {$req->getSendingUser()}! /msg '!help' to me to see a list of available commands."
            ));
        }
    }


    /**
     * Tests the message for the hi words followed by the bot's nickname.
     *
     * @param string $msg  The IRC message
     * @param string $nick The bot's nickname
     *
     * @return bool True if someone said hi to the bot, false otherwise
     */
    private function test($msg, $nick) {
        $re = '/' . implode('|', $this->_hi_words) . '\s+' . $nick . '/i';    
        return (bool) preg_match($re, $msg); 
    }
}

