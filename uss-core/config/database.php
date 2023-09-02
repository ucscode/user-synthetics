<?php

defined('CONFIG_DIR') || DIE;

/**
* Connect to the database.
*
* This method establishes a connection to the database using the credentials defined in the `conn.php` file.
* If the `DB_CONNECT` constant is set to `false`, the database connection will be ignored.
*
* @category Database
* @access private
*
* @return void
* @ignore
*/

if(DB_CONNECTION_ENABLED) {

    try {

        self::$global['mysqli'] = @new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        if(self::$global['mysqli']->connect_errno) {

            self::tag('connect_error', self::$global['mysqli']->connect_error);

            self::view(function () {
                require_once VIEW_DIR . '/db-conn-failed.php';
            });

            self::instance()->exit();

        } else {

            $__options = new Pairs(self::$global['mysqli'], DB_TABLE_PREFIX . "_options");

            self::$global['options'] = $__options;

        };

    } catch(Exception $e) {

        self::tag('connect_error', $e->getMessage());

        self::view(function () {
            require_once VIEW_DIR . "/db-conn-failed.php";
        });

        self::instance()->exit();

    }

} else {
    self::$global['mysqli'] = self::$global['options'] = null;
}
