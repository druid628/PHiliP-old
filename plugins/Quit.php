<?php

/**
 * A plugin to tell the bot to quit.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */

namespace PHiliP\Plugin;

use PHiliP\BaseBotCommand;
use PHiliP\IRC\Request;
use PHiliP\IRC\Response;

class Quit extends BaseBotCommand {

    /** @var array $_allowedUsers An array of nicks allowed to quit the bot */
    private $_allowedUsers = array();


    /**
     * Constructor.
     *
     * @see BaseBotCommand#__construct()
     */
	public function __construct($command = '', $captures = '', $description = '') {
		parent::__construct(
			'quit',
			'(.*)',
			'This command will disconnect the bot from the server'
		);
	}


    /**
     * Sets up the array of allowed users.
     *
     * @see BotPlugin#init()
     */
    public function init($options = array()) {
        $this->_allowedUsers = explode(',', $options['allowed_users']);
    }


    /**
     * The bot will quit, if the requestor is allowed to tell it to.
     *
     * @see BaseBotCommand#handle()
     */
	public function handle($req, $matches) {
		if ($req->isPrivateMessage() && $this->isAllowed($req->getSendingUser())) {
			return new Response(
				'QUIT',
				$matches[1]
			);
		}
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
