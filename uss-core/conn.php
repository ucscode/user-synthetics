<?php


define("DB_CONNECT", true); // `FALSE` - if you don't intend to access database;


// -------- [{ Manage DataBase Configuration }] ------------


if($_SERVER['SERVER_NAME'] == 'localhost'):

    # --- [{ FOR LOCALHOST ONLY }] ---

    define("DB_HOST", "localhost");
    define("DB_USER", 'root');
    define("DB_PASSWORD", '');
    define("DB_NAME", 'uss_v2c');

else:

    # --- [{ FOR SERVER HOST ONLY }] ---

    define("DB_HOST", "localhost"); //
    define("DB_USER", '');
    define("DB_PASSWORD", '');
    define("DB_NAME", '');

endif;


// --------- [{ DataBase Table Prefix }] ----------

define("DB_TABLE_PREFIX", 'uss');
