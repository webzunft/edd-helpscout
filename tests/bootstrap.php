<?php

echo "Welcome to the EDD HelpScout testsuite." . PHP_EOL;

define( 'EDD_HELPSCOUT_API_PATH', '/my-api-path' );
define( 'HELPSCOUT_SECRET_KEY', 'my-random-string' );

require __DIR__ . '/../vendor/autoload.php';