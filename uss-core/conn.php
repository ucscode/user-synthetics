<?php

# Set the constant below to `false` if you don't intend to access database;
define("DB_CONNECTION_ENABLED", true);

if($_SERVER['SERVER_NAME'] === 'localhost') {

    # Localhost connection
    define("DB_HOST", "localhost");
    define("DB_USER", 'root');
    define("DB_PASSWORD", '');
    define("DB_NAME", 'uss_undefined');

} else {

    # Server Connection
    define("DB_HOST", "localhost");
    define("DB_USER", '');
    define("DB_PASSWORD", '');
    define("DB_NAME", '');

};

# Database prefix for tables
define("DB_TABLE_PREFIX", 'uss');
