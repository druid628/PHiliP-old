<?php

/**
 * A plugin inspired by South Park
 *
 * @author Micah Breedlove <druid628@gmail.com>
 */
class TrapperKeeper extends ioBaseIRCCommand {
    private $people = array();

    /**
     * Calls the parent constructor to set up the pattern and the description.
     *
     * @see ioBaseIRCCommand#__construct()
     */
    public function __construct($pattern = '', $description = '') {
        parent::__construct(
            '/^!trapperKeeper, \b(\w+)\b/',
            'TrapperKeeper does stuff for you.'
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
