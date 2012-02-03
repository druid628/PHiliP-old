#!/usr/bin/env php
<?php

/**
 * PHiliP -- a time waster by Bill Israel <bill.israel@gmail.com>
 *
 * PHiliP is an extensible PHP-based IRC bot and a work in progress.
 */

require_once(dirname(__FILE__) . '/src/ioAutoloader.php');

$autoloader = new ioAutoloader(array(
    __DIR__ . '/src',
    __DIR__ . '/src/plugins'
));
$autoloader->register();

// Load config and list plugins
$config = parse_ini_file('config/config.ini', true);
$plugins = array(
    new FirePeople()
);

// Create copy of PHiliP app
$handler = new ioIRCCommandHandler($plugins);
$philip = new ioIRCBot($config['irc'], $handler);

// Connect to IRC and join channel(s)
if ($philip->connect()) {
    $philip->listen();
} else {
    echo 'ERROR: Unable to connect to IRC server' . PHP_EOL;
    exit(1);
}

