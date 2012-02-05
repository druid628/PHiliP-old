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
	public function __construct($command = '', $captures = '', $description = '') {
		parent::__construct(
			'quit',
			'',
			'This command will disconnect the bot from the server'
		);
	}


	public function handle($req, $matches) {
		if ($req->isPrivateMessage()) {
			return new Response(
				'QUIT',
				'Because ' . $req->getSendingUser() . ' told me to.'
			);
		}
	}
}
