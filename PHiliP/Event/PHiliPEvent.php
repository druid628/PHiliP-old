<?php

namespace PHiliP\Event;

/**
 * Extends the sfEvent object to always treat the return value as an array
 * of responses.
 */

use \sfEvent;

class PHiliPEvent extends sfEvent {
    /** @var array $_responses The responses to send back to IRC. */
    private $_responses = array();

    /**
     * Overrides the setReturnValue method to maintain an array
     * of responses.
     *
     * @see sfEvent#setReturnValue()
     */
    public function setReturnValue($value) {
        if (is_array($value)) {
            $this->_responses = array_merge($this->_responses, $value);
        } else {
            array_push($this->_responses, $value); 
        }
    }

    /**
     * Returns the array of responses.
     *
     * @see sfEvent#getReturnValue()
     */
    public function getReturnValue() {
        return $this->_responses;
    }
}
