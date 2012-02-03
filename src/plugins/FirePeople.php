<?php

/**
 * A playful plugin for "firing" people.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */
class FirePeople extends ioBaseIRCCommand {
    private $people = array();

    /**
     * Calls the parent constructor to set up the pattern and the description.
     *
     * @see ioBaseIRCCommand#__construct()
     */
    public function __construct($pattern = '', $description = '') {
        parent::__construct(
            '/^!fire \b(\w+)\b/',
            'This command fires Jarvis. And only fires Jarvis.'
        );

        $this->startDate = date('m/d/Y');
    }

    /**
     * "Fires" people.
     *
     * @see ioBaseIRCCommand#handle()
     */
    public function handle($data) {
        $matches = array();
        preg_match($this->_pattern, $data[ioIRCConstants::$IRC_MSG], $matches);
        $who = $matches[1];

        if (isset($this->people[$who])) {
            $this->people[$who] += 1;
        } else {
            $this->people[$who] = 1;
        }

        $times = ($this->people[$who] == 1) ? 'time' : 'times';
        return "$who, you're fired. That's {$this->people[$who]} $times since {$this->startDate}. Keep it up, asshole.";
    }
}
