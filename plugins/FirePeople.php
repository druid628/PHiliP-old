<?php

/**
 * A playful plugin for "firing" people.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */

namespace PHiliP\Plugin;

use PHiliP\BaseBotCommand;

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
    public function handle($data, $matches) {
        $who = $matches[0];
        $who_key = strtolower($who);

        if (isset($this->people[$who_key])) {
            $this->people[$who_key] += 1;
        } else {
            $this->people[$who_key] = 1;
        }

        $times = ($this->people[$who_key] == 1) ? 'time' : 'times';
        return "$who, you're fired. That's {$this->people[$who_key]} $times since {$this->startDate}. Keep it up, asshole.";
    }
}
