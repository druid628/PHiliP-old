<?php

/**
 * Does something
 *
 * @author Micah Breedlove <druid628@gmail.com>
 */

namespace PHiliP\Plugin;

use PHiliP\BaseBotCommand;
use PHiliP\IRC\Request;
use PHiliP\IRC\Response;

class TrapperKeepr extends BaseBotCommand {
    private $commands = array(
        "country music, full volume" => "begins to play country music at full volume.",
        );

    /**
     * Calls the parent constructor to set up the pattern and the description.
     *
     * @see BaseBotCommand#__construct()
     */
    public function __construct($command = '', $captures = '', $description = '') {
        parent::__construct(
            'TrapperKeepr',
            '\b(.+)+\b',
            'TrapperKeepr does stuff for you.'
        );

    }

    /**
     * Fire people.
     *
     * @see BaseBotCommand#handle()
     */
    public function handle($req, $matches) {
        $what = $matches[0];

        
		      /*return new Response('PRIVMSG', array(
			      $req->getSource(),
			      $what
		      ));*/
        if (isset($this->commands[$what])) {
          $msg = "\001ACTION " . $this->commands[$what] . "\001";

		      return new Response('PRIVMSG', array(
			      $req->getSource(),
			      $msg
		      ));
        }
    }
}
