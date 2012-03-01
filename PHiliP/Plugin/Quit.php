<?php

namespace PHiliP\Plugin;

/**
 * A plugin to tell the bot to quit.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */

use \sfEvent;

use PHiliP\IRC\Request;
use PHiliP\IRC\Response;
use PHiliP\Plugin\BotPlugin;

class Quit extends BotPlugin {

    /** @var array $_allowedUsers An array of nicks allowed to quit the bot */
    private $_allowedUsers;


    /**
     * Constructor.
     */
	public function __construct($dispatcher) {
        $this->registerCommand($dispatcher,
            'quit',
            '/(.*)/',
            '!quit <msg>: Disconnect the bot from the server.'
        );
	}


    /**
     * Sets up the array of allowed users.
     */
    public function init($options = array()) {
        $this->_allowedUsers = array_map('trim', explode(', ', $options['allowed_users']));
    }


    /**
     * The bot will quit, if the requestor is allowed to tell it to.
     *
     * @see BotPlugin#handle()
     */
	public function handle($req, $conf, $matches) {
		if ($req->isPrivateMessage() && $this->isAllowed($req->getSendingUser())) {
            $msg = $matches[0] == '!quit' ? 'Goodbye.' : $matches[0];
            return new Response('QUIT', $msg);
        }

        return new Response('PRIVMSG', array(
            $req->getSource(),
            "You're not the boss of me."
        ));
	}


    /**
     * Determines if the requesting user is allowed to tell the bot to quit.
     *
     * @param string $who The person telling the bot to quit
     *
     * @return bool True if the person is allowed to do this
     */
    private function isAllowed($who) {
        return in_array($who, $this->_allowedUsers);
    }
}
