<?php

namespace PHiliP\Plugin;

/**
 * This class will handle most of the mundane IRC commands that the
 * typical bot plugin won't care about.
 *
 * NOTE:
 * This class is a "bad" plugin and doesn't conform to the typical
 * format/behavior of a PHiliP plugin. Please don't use it as an
 * example of how to write a plugin.
 */

use \sfEvent;

use PHiliP\IRC\Request;
use PHiliP\IRC\Response;
use PHiliP\Plugin\BotPlugin;

class IRCCommands extends BotPlugin {
    
    /**
     * Constructor
     * 
     * @param sfEventDispatcher $dispatcher The event dispatcher
     *                                      to attach our event hanlders to
     */
    public function __construct($dispatcher) {
        $dispatcher->connect('server.command.ping', array($this, 'handlePing'));
    }

    /**
     * Returns the help message for this plugin.
     *
     * @see PHiliP\Plugin\BotPlugin#getHelpMessage()
     */
    public function getHelpMessage() {
        return "Responds to IRC server messages.";
    }

    /**
     * Responds to IRC PING commands with an appropriate PONG. 
     * 
     * @param sfEvent $event The event object passed by the event dispatcher
     *
     * @return Response The response to send back to the IRC server
     */
    public function handlePing(sfEvent $event) {
        $req = $event['request'];

        $event->setReturnValue(
            new Response('PONG', $req->getMessage())
        );
    }
}
