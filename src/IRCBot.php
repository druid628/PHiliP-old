<?php

/**
 * Class that handles the connecting and listening to the IRC channel.
 * The class for handling messages is passed in to the constructor.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */

namespace PHiliP;

use PHiliP\IRC\Request;
use PHiliP\IRC\Response;

class IRCBot {
    private $_handler;
    private $_config;
    private $_socket;
    


    /**
     * Constructor
     *
     * @param array  $config  Parsed config.ini file as array
     * @param object $handler An object that can parse IRC commands
     */
    public function __construct($config, $handler) {
       $this->_config = $config; 
       $this->_handler = $handler;
    }

    /**
     * Destructor - ensures the object is destroyed and the socket closed before exiting
     */
    public function __destruct() {
        fclose($this->_socket);
        fwrite(STDOUT, PHP_EOL . '## Disconnected. ##' . PHP_EOL);
    }


    /**
     * Connects to the IRC server and port specified in the config.ini file
     */
    public function connect() {
        $this->_socket = fsockopen($this->_config['host'], $this->_config['port']);
        stream_set_blocking(STDIN, 0);

		if ($this->_socket) {
			$this->login();
			$this->joinChannel();
			return true;
		}

        return false;
    }


    /**
     * Attempts to log in as the user/nick specificed in the config.ini file
     */
    private function login() {
		$this->send(new Response('USER', array(
			$this->_config['nick'],
			$this->_config['hostname'],
			$this->_config['servername'],
			$this->_config['realname']
		)));

		$this->send(new Response('NICK', $this->_config['nick']));
    }


    /**
     * JOINs the channel specified in the config.ini file.
     */
    private function joinChannel() {
		$this->send(new Response('JOIN', $this->_config['channel']));
    }


    /**
     * Listen on a loop and handle commands as they come in.
     */
    public function listen() {
        do {
            $data = fgets($this->_socket, 255);
			if (!empty($data)) {
				fwrite(STDOUT, '--> ' . $data);
				$cmd = new Request($data);
				if ($response = $this->_handler->handle($cmd)) {
					$this->send($response);
				}
			}
        } while(!feof($this->_socket));
    }


    /**
     * Actually push data back into the socket (giggity).
     */
    private function send($response) {
        fwrite($this->_socket, $response . "\r\n");
        fwrite(STDOUT, '<-- ' . $response . PHP_EOL);
    }
}
