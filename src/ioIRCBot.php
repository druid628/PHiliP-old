<?php

/**
 * Class that handles the connecting and listening to the IRC channel.
 * The class for handling messages is passed in to the constructor.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */
class ioIRCBot {
    private $_handler;
    private $_config;
    private $_socket;
    
    private $RE_IRC_MSG = '/^(?:[:@]([^\\s]+) )?([^\\s]+)(?: ((?:[^:\\s][^\\s]* ?)*))?(?: ?:(.*))?$/'; 


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

        return (bool) $this->_socket && $this->login() && $this->joinChannel();
    }

    /**
     * Attempts to log in as the user/nick specificed in the config.ini file
     */
    private function login() {
        $this->send('USER', $this->_config['nick'] . ' gmail.com ' . ' gmail.com :' . $this->_config['name']);
        $this->send('NICK', $this->_config['nick']);

        // TODO: Add error handling
        return true;
    }

    /**
     * JOINs the channel specified in the config.ini file.
     */
    private function joinChannel() {
        // join the specificied IRC channels
        $this->send('JOIN', $this->_config['channel']);

        // TODO: Add error handling
        return true;
    }

    /**
     * Listen on a loop and handle commands as they come in.
     */
    public function listen() {
        do {
            $data = fgets($this->_socket, 255);
            if ($response = $this->handle($data)) {
                $this->send($response);
            }
        } while(!feof($this->_socket));
    }

    /**
     * Handle incoming data.
     */
    public function handle($data) {
        $parts = $this->parse($data);
        $response = false;

        // Get the IRC command from the message, and send it to a handler
        if (isset($parts[ioIRCConstants::$IRC_CMD])) {
            $cmd = strtolower($parts[ioIRCConstants::$IRC_CMD]);

            if (method_exists($this->_handler, $cmd)) { 
                $response = $this->_handler->$cmd($parts);
            } elseif (is_numeric($cmd)) {
                // nothing to do here... 
            } else {
                fwrite(STDOUT, "[WARN] No handler for command: $cmd" . PHP_EOL);
            }
        }
        
        if ($response) {
            $this->send($response);
        }
    }

    /**
     * Parse/clean the IRC command and return it in parts.
     */
    private function parse($data) {
        if (empty($data)) { return array(); }

        // TODO: Wrap these in debug code
        fwrite(STDOUT, '<-- ' . $data);

        $matches = array();
        preg_match($this->RE_IRC_MSG, $data, $matches);

        // Remove newlines and carriage returns
        for($i = count($matches) - 1; $i >= 0; $i--) {
           $matches[$i] = str_replace(array(chr(10), chr(13)), '', $matches[$i]); 
        }

        return $matches;
    }

    /**
     * Actually push data back into the socket (giggity).
     */
    private function send($cmd, $msg = null) {
        $msg = $msg ?: '';

        fwrite($this->_socket, $cmd . ' ' . $msg . "\r\n");
        // TODO: Wrap these in debug code
        fwrite(STDOUT, '--> ' . $cmd . ' ' . $msg . PHP_EOL);
    }
}
