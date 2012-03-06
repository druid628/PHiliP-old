#!/usr/bin/env php
<?php

require_once __DIR__ . '/PHiliP/Autoloader.php';

/**
 * This is the PHiliP bootstrap file. It should instantiate the autoloader,
 * read the configuration file, and then pass control to PHiliP.
 *
 * @author Bill Israel <bill.israel@gmail.com>
 */

use PHiliP\Autoloader;
use PHiliP\PHiliP;

$autoloader = new Autoloader(array(
    __DIR__,
    __DIR__ . '/lib/Pimple',
    __DIR__ . '/lib/sfEventDispatcher/lib'
));
$autoloader->register();

$config = parse_ini_file('config/config.ini', true);
$bot = new PHiliP($config);
$bot->run();

