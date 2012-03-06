<?php

namespace PHiliP;

/**
 * The PHiliP IRC bot.
 * Commands are handled via events and are handled by way of the plugins.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */

use PHiliP\Utilities;
use PHiliP\IRC\Request;
use PHiliP\IRC\Response;
use PHiliP\Event\PHiliPEvent;

use \Pimple;
use \sfEventDispatcher;

class PHiliP extends Pimple {

    /** @var The socket connected to the IRC server. */
    private $_socket;

    /**
     * Constructor.
     * 
     * @param array $config The configuration array
     */
    public function __construct($config = array()) {
        $this['config'] = $config;
        $this['dispatcher'] = $this->share(function() {
            return new sfEventDispatcher();
        });
        $this['plugins'] = array();

        $this->loadPlugins();
    }


    /**
     * Desctructor - ensures the socket is closed before exiting.
     */
    public function __destruct() {
        if (isset($this->_socket)) {
            fclose($this->_socket);
        }

        fwrite(STDOUT, PHP_EOL . "## Disconnected ##" . PHP_EOL);
    }


    /**
     * Connects and logs into the IRC server defined by
     * the configuration, listens for connections and sends
     * events when messages are received. 
     */
    public function run() {
        if ($this->connect()) {
            $this->login();
            $this->join();
            $this->listen();   
        } else {
            fwrite(STDOUT, PHP_EOL . '## ERROR: Unable to connect to IRC server. ##' . PHP_EOL);
        }
    }
   

    /**
     * Loads the enabled plugins from configuration.
     * 
     * TODO: Is there a better way to load these?
     */
    public function loadPlugins() {
        $plugins = $this['plugins'];
        foreach($this['config']['plugins'] as $p => $enabled) {
            if ($enabled) {
                // Build the fully-namespaced class, then use it.
                // TODO: is there a better way to handle this?
                $ns = "PHiliP\Plugin\\" . $p;
                $class = new $ns($this['dispatcher']);

                // If there's configuration to be done, do it.
                if (isset($this['config'][$p])) {
                    $class->init($this['config'][$p]);
                }

                $plugins[] = $class;
            }
        }
    }


    /**
     * Connects to the IRC server.
     *
     * @return bool True if the connection succeeded, false otherwise
     */
    private function connect() {
        $irc = $this['config']['irc'];
        $this->_socket = fsockopen($irc['hostname'], $irc['port']);
        stream_set_blocking(STDIN, 0);
        
        return (bool) $this->_socket;
    }


    /**
     * Sets the nickname/real name, etc for the bot.
     * Values are defined in configuration.
     */
    private function login() {
        $irc = $this['config']['irc'];
        $this->send(new Response('USER', array(
			$irc['nick'],
			$irc['hostname'],
			$irc['servername'],
			$irc['realname']
		)));

		$this->send(new Response('NICK', $irc['nick']));
    }


    /**
     * Joins the channel(s) specified in the configuration.
     */
    private function join() {
        $irc = $this['config']['irc'];
		$this->send(new Response('JOIN', $irc['channel']));
    }


    /**
     * Listens in a loop and fires off events as messages
     * arrive from the IRC server.
     */
    private function listen() {
        do {
            $data = fgets($this->_socket, 512);
			if (!empty($data)) {
				fwrite(STDOUT, '--> ' . $data);
				$req = new Request($data);
                $cmd = strtolower($req->getCommand());

                $msg = $req->getMessage();
                if ($cmd === 'privmsg' && $this->isBotCommand($msg)) {
                    $break = strpos($msg, ' ');
                    $msg = ($break !== false) ? substr($msg, 0, $break) : $msg;
                    $bot_cmd = ltrim($msg, '!');
                    $event_name = "bot.command.$bot_cmd";
                } else {
                    $event_name = "server.command.$cmd";
                }

                $event = new PHiliPEvent($this, $event_name, array(
                    'config'     => $this['config'],
                    'request'    => $req
                ));
                $this['dispatcher']->notify($event);

                // Send whatever the response is to the socket
                $messages = $event->getReturnValue();
                if (!empty($messages)) {
                    foreach($messages as $message) {
                        $this->send($message);
                    }
                }
			}
        } while (!feof($this->_socket));
    }


    /**
     * Actually push data back into the socket (giggity).
     */
    private function send($response) {
        fwrite($this->_socket, $response . "\r\n");
        fwrite(STDOUT, '<-- ' . $response . PHP_EOL);
    }


    /**
     * Returns whether the given message is a bot command,
     * as determined by whether it starts with a !
     *
     * @param string $msg The message to test
     *
     * @return bool True if the message is a bot command, false otherwise
     */
    private function isBotCommand($msg) {
        return strpos($msg, '!') === 0;
    }
}
