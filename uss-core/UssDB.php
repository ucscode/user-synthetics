<?php

// Enable or disable database connection

define('DB_ENABLED', true);

// Database Table Prefix

define('DB_PREFIX', 'uss_');

// Local Development

if(in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1'], true)) {

    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '12345678');
    define('DB_NAME', 'www_ussd');

} else {

    // Remote Development

    define('DB_HOST', 'localhost');
    define('DB_USER', '');
    define('DB_PASSWORD', '');
    define('DB_NAME', '');

};
