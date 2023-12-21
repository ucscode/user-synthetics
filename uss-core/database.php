<?php
/**
 * database.php - Database Configuration
 *
 * This file defines constants related to database configuration, including whether the
 * database is enabled, the table prefix, and connection details based on the server
 * environment (local or remote development).
 *
 * @package Ucscode\Uss
 */

/**
 * Flag indicating whether the database is enabled or disabled.
 *
 * @var bool
 */
define('DB_ENABLED', true);

/**
 * Prefix for database tables.
 *
 * @var string
 */
define('DB_PREFIX', 'uss_');

// Check server environment for database connection details

if (in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1'], true)) {

    /**
     * Database connection details for local development.
     */
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '12345678');
    define('DB_NAME', 'www_uss');

} else {

    /**
     * Database connection details for remote development.
     */
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '12345678');
    define('DB_NAME', 'www_uss');

}
