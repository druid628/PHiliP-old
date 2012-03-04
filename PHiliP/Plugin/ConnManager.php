<?php

namespace PHiliP\Plugin;

/**
 * A plugin for managing room joining, parting and quitting.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */

use PHiliP\IRC\Response;
use PHiliP\Plugin\BotPlugin;

class ConnManager extends BotPlugin {

    /** @var array $_allowedUsers An array of nicks allowed to quit the bot */
    private $_allowedUsers;


    /**
     * Constructor.
     */
    public function __construct($dispatcher) {
        // Quits the bot
        $this->registerCommand($dispatcher,
            'quit',
            '/(.*)/',
            '!quit <msg>: Disconnect the bot from the server.'
        );

        // Leaves Channels
        $this->registerCommand($dispatcher,
            'part',
            '/(.*)/',
            '!part <channel-1> ... <channel-n>: Part/Leave the specified rooms'
        );

        // Leaves Channels
        $this->registerCommand($dispatcher,
            'leave',
            '/(.*)/',
            '!leave <channel-1> ... <channel-n>: Part/Leave the specified rooms'
        );

        // Joins Channels
        $this->registerCommand($dispatcher,
            'join',
            '/(.*)/',
            '!join <channel-1> ... <channel-n>: Join the specified rooms'
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
        $resp = new Response('PRIVMSG', array(
            $req->getSendingUser(),
            "You're not the boss of me."
        ));

		if ($req->isPrivateMessage() && $this->isAllowed($req->getSendingUser())) {
            $handler = 'handle' . ucfirst($this->getBotCommand($req->getMessage()));
            $resp = $this->$handler($matches);
        }

        return $resp;

	}


    /**
     * Handles PART and LEAVE commands.
     *
     * @param array $matches The array of matches found via
     *                       the captures defined when creating the command.
     * 
     * @return Response The IRC Response to send back to the server.
     */
    private function handlePart($matches) {
        $channels = str_replace(' ', ',', $matches[0]);
        return new Response('PART', $channels);
    }


    /**
     * Handles PART and LEAVE commands.
     *
     * @param array $matches The array of matches found via
     *                       the captures defined when creating the command.
     * 
     * @return Response The IRC Response to send back to the server.
     */
    private function handleLeave($matches) {
        return $this->handlePart($matches);
    }


    /**
     * Handles JOIN commands.
     *
     * @param array $matches The array of matches found via
     *                       the captures defined when creating the command.
     * 
     * @return Response The IRC Response to send back to the server.
     */
    private function handleJoin($matches) {
        $channels = str_replace(' ', ',', $matches[0]);
        return new Response('JOIN', $channels);
    }


    /**
     * Handles QUIT commands.
     *
     * @param array $matches The array of matches found via
     *                       the captures defined when creating the command.
     * 
     * @return Response The IRC Response to send back to the server.
     */
    private function handleQuit($matches) {
        $msg = empty($matches[0]) ? 'Goodbye.' : $matches[0];
        return new Response('QUIT', $msg);
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
