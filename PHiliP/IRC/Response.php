<?php

/**
 * A simplified representation of an IRC response object.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */

namespace PHiliP\IRC;

class Response {
	
	private $_cmd;
	private $_args;

	/**
	 * Constructor.
	 *
	 * @param string $cmd  The IRC command to return
	 * @param mixed  $args The arguments to send with it
	 */
	public function __construct($cmd, $args = '') {
		$this->_cmd = strtoupper($cmd);

		if (!is_array($args)) {
			$args = array($args);
		}
		
		$this->_args = $args;
		$end = count($this->_args) - 1;
		$this->_args[$end] = ':' . $this->_args[$end];
	}

	/**
	 * Stringify this object.
	 *
	 * @return string The string representation of the response
	 */
	public function __toString() {
		return $this->_cmd . ' ' . implode(' ', $this->_args);
	}
}
