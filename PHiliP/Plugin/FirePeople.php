<?php

namespace PHiliP\Plugin;

/**
 * A playful plugin for "firing" people.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */

use \sfEvent; 

use PHiliP\IRC\Request;
use PHiliP\IRC\Response;
use PHiliP\Plugin\BotPlugin;

class FirePeople extends BotPlugin {

	/**
	 * @var array $_people The array of _people who've been fired
	 *		  			   and how many times they've been fired.
	 */
    private $_people = array();

    /** @var date $start_date The Date the object was created */
    private $_start_date;


    /**
	 * Attaches to the bot.command.fire event.
     */
    public function __construct($dispatcher) {
        $this->_captures = '/\b(\w+)\b/';
        $this->_help_msg = "!fire <someone>: Keeps count of how many times someone's been fired.";
        $this->_start_date = date('m/d/Y');

		$dispatcher->connect('bot.command.fire', array($this, 'handle'));
        $this->registerHelp($dispatcher);
    }


    /**
     * Fire someone.
	 *
	 * @param sfEvent $event The event to handle
     */
    public function handle(sfEvent $event) {
        $req = $event['request'];
        $matches = $this->parse($req->getMessage());

        $who = $matches[0];
        $key = strtolower($who);

        if (isset($this->_people[$key])) {
            $this->_people[$key] += 1;
        } else {
            $this->_people[$key] = 1;
        }

		$count = $this->_people[$key];
        $times = ($count === 1) ? 'time' : 'times';
        $msg = "$who, you're fired! That's $count $times since {$this->_start_date}. Keep it up, asshole.";
		$event->setReturnValue(new Response('PRIVMSG', array($req->getSource(), $msg)));
    }
}
