<?php

defined('CONFIG_DIR') || DIE;

/**
    * Prepare the essential variables required for the formation of user synthetics;
    * These variables may be overridden by other modules
    */
self::__vars();


/**
    * Establishes a database connection.
    * The connection file `conn.php` is used to establish the database connection.
    * If the `DB_CONNECT` constant is set to `false`, the database connection will be ignored.
    */
self::__connect();


/**
    * Initializes the session.
    * Sessions are highly significant in PHP and provide important functionality.
    * Although cookies are great, PHP sessions offer additional capabilities.
    * Don't like sessions? Then you should consider deleting User Synthetics (just kidding!).
    */
self::__session();


/**
    * Marks the end of the initialization process.
    * This signifies the completion of the initialization phase.
    */
self::$init = true;