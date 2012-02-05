<?php

/**
 * A playful plugin for "firing" people.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */

namespace PHiliP\Plugin;

use PHiliP\BaseBotCommand;
use PHiliP\IRC\Request;
use PHiliP\IRC\Response;

class FirePeople extends BaseBotCommand {
    private $people = array();

    /**
     * Calls the parent constructor to set up the pattern and the description.
     *
     * @see BaseBotCommand#__construct()
     */
    public function __construct($command = '', $captures = '', $description = '') {
        parent::__construct(
            'fire',
            '\b(\w+)\b',
            'This command "fires" people, and keeps a count of how many times they\'ve been fired.'
        );

        $this->startDate = date('m/d/Y');
    }

    /**
     * Fire people.
     *
     * @see BaseBotCommand#handle()
     */
    public function handle($req, $matches) {
        $who = $matches[0];
        $key = strtolower($who);

        if (isset($this->people[$key])) {
            $this->people[$key] += 1;
        } else {
            $this->people[$key] = 1;
        }

		$count = $this->people[$key];
        $times = ($count === 1) ? 'time' : 'times';
        $msg = "$who, you're fired. That's $count $times since {$this->startDate}. Keep it up, asshole.";
		return new Response('PRIVMSG', array(
			$req->getSource(),
			$msg
		));
    }
}
