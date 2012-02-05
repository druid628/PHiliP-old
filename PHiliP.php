#!/usr/bin/env php
<?php

/**
 * PHiliP -- a time waster by Bill Israel <bill.israel@gmail.com>
 *
 * PHiliP is an extensible PHP-based IRC bot and a work in progress.
 */

require_once(__DIR__ . '/src/Autoloader.php');

use PHiliP\Autoloader;
use PHiliP\IRCCommandHandler;
use PHiliP\IRCBot;

$autoloader = new Autoloader(array(
    __DIR__ . '/src',
    __DIR__ . '/plugins'
));
$autoloader->register();

// Load config and list plugins
$plugins = array();
$config = parse_ini_file('config/config.ini', true);
foreach ($config['plugins'] as $plugin => $enabled) {
    if ($enabled) {
        $ns_plugin  = '\PHiliP\Plugin\\' . $plugin;
        $plugins[]  = $new_plugin = new $ns_plugin();

        // If there's initialization to do, do it here.
        if (isset($config[$plugin]) && is_array($config[$plugin])) {
            $new_plugin->init($config[$plugin]);
        }
    }
}

// Create copy of PHiliP app
$handler = new IRCCommandHandler($plugins);
$philip = new IRCBot($config['irc'], $handler);

// Connect to IRC and join channel(s)
if ($philip->connect()) {
    $philip->listen();
} else {
    echo 'ERROR: Unable to connect to IRC server' . PHP_EOL;
    exit(1);
}

