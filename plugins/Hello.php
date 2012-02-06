<?php

/**
 * Just a simple auto-responder, and a basic example of
 * implementing a bot listener.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */

namespace PHiliP\Plugin;

use PHiliP\BaseBotListener;
use PHiliP\IRC\Request;
use PHiliP\IRC\Response;

class Hello extends BaseBotListener {

	/**
	 * Constructor
	 *
	 * @see BaseBotListener#__construct()
	 */
	public function __construct($pattern = '', $description = '') {
		// we need to pull in the global config for this 
		global $config;

		parent::__construct(
			'\b' . $config['irc']['nick'] . '\b',
			'Auto-responds to mentions of the bot\'s name'
		);
	}


	/**
	 * A simple auto-reponders that says hi when its name is mentioned.
	 *
	 * @see BasePlugin#handle()
	 */
	public function handle($req, $matches) {
		return new Response('PRIVMSG', array(
			$req->getSource(),
			"Hi {$req->getSendingUser()}. Can I help you with something?"
		));
	}

}
