<?php

namespace PHiliP;

/**
 * A collection of static utility functions that're used
 * in multiple places in PHiliP.
 * 
 * @author Bill Israel <bill.israel@gmail.com>
 */

class Utilities {

    /**
     * Returns whether the given message is a bot command,
     * as determined by whether it starts with a !
     *
     * @param string $msg The message to test
     *
     * @return bool True if the message is a bot command, false otherwise
     */
    public static function isBotCommand($msg) {
        return strpos($msg, '!') === 0;
    }
}
