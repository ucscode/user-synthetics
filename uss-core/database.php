<?php

define('DB_ENABLED', true); // false to disabled

// Database Table Prefix

define('DB_PREFIX', 'uss_');

if(in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1'], true)) {

    /**
     * For Local Development Only
     */
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '12345678');
    define('DB_NAME', 'www_uss');

} else {

    /**
     * For Remote Development Only
     */
    define('DB_HOST', 'localhost');
    define('DB_USER', '');
    define('DB_PASSWORD', '');
    define('DB_NAME', '');

};
