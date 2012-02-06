<?php

/**
 * A command for telling the bot to leave a channel.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */

namespace PHiliP\Plugin;

use PHiliP\BaseBotCommand;
use PHiliP\IRC\Request;
use PHiliP\IRC\Response;

class Leave extends BaseBotCommand {

    /** @var array $_allowedUsers An array of nicks allowed issue this command */
	private $_allowedUsers;
	

	/**
	 * Constructor
	 *
	 * @see BaseBotCommand#__construct()	
	 */
	public function __construct($command = '', $captures = '', $description = '') {
		parent::__construct(
			'leave',
			'',
			'Instructs the bot to leave the room.'
		);
	}


	/**
	 * Initializes the array of allowed users.
	 *
	 * @see BotPlugin#init()
	 */
	public function init($options = array()) {
		$this->_allowedUsers = array_map('trim', explode(',', $options['allowed_users']));
	}


	/**
	 * Joins the room specified.
	 *
	 * @see BotPlugin#handle()
	 */
	public function handle($req, $matches) {
		$channel = $req->getSource();
		if ($this->isAllowed($req->getSendingUser())) {
			return new Response('PART', $channel);
		}

		return new Response('PRIVMSG', array($req->getSource(), "You can't tell me what to do."));
	}


    /**
     * Determines if the requesting user is allowed to issue the bot this command.
     *
     * @param string $who The person telling the bot to do something
     *
     * @return bool True if the person is allowed to do this
     */
    private function isAllowed($who) {
        return in_array($who, $this->_allowedUsers);
    }
}
