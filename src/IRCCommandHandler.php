<?php

/**
 * A simple handler for the basic IRC commands.
 * Each IRC command should map to a function of the same name.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */

namespace PHiliP;

use PHiliP\IRC\Request;
use PHiliP\IRC\Response;

class IRCCommandHandler {

    /** @var array $_plugins The list of known plugins */
    public $_plugins;

    /**
     * Accepts an array of plugins for handling bot commands.
     * Plugins are expected to extend from ioBaseIRCCommand.php
     *
     * @param array $plugins An array of PHiliP IRC bot commands
     */
    public function __construct($plugins = array()) {
        $this->_plugins = $plugins;
    }


    /**
     * If there's a command, and there's a method here that
     * matches the command name, send the request to it to 
     * handle.
     *
     * @param Request $request The IRC request object
     *
     * @return mixed The IRC Response object to send, or false otherwise
     */
    public function handle($request) {
        if ($cmd = strtolower($request->getCommand())) {
            if (method_exists($this, $cmd)) {
                return $this->$cmd($request);
            } 
        }
        
        return false;
    }


    /**
     * PING command handler; just responds to PING commands with an appropriate PONG.
     *
     * @param Request $req The IRC Request object
     *
     * @return Response The IRC Response object to send back
     */
    private function ping($req) {
        return new Response('PONG', $req->getMessage());
    }


    /**
     * For now, don't handle this gracefully, just panic and exit.
     *
     * @param Request $req The IRC Request object
	 *
	 * @return Response A QUIT IRC Response object
     */
    private function error($req) {
		return new Response('QUIT', 'Received ERROR');
    }


    /**
     * PRIVMSG commands will loop through the list of given plugins
     * and try to find a bot command that knows how to handle a line like this.
     * 
     * @param Request $req The IRC Request object
     *
     * @return mixed The IRC Response object to send, or false otherwise
     */
    private function privmsg($req) {
        foreach ($this->_plugins as $plugin) {
            if ($plugin->test($req->getMessage())) {
                $matches = $plugin->parse($req->getMessage());
                return $plugin->handle($req, $matches);
            }
        }

        return false;
    }
}
