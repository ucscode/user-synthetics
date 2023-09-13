<?php

/**
 * Enable or disable database connection
 */ 
define('DB_ENABLED', true);

/**
 * Database Table Prefix
 */
define('DB_PREFIX', 'uss_');

/** 
 * Local Server Only 
 */
if($_SERVER['SERVER_NAME'] === 'localhost') {

    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'uss_test');

} else {

    /**
     * Web Server Only
     */
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', '');

};

