<?php

defined('CONFIG_DIR') || die;

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

        $this->mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        if($this->mysqli->connect_errno) {

            $this->render('@Uss/db.error.html.twig', [
                'error' => $this->mysqli->connect_error,
                'ussInstance' => $this
            ]);

            die;

        } else {

            $this->options = new Pairs($this->mysqli, DB_TABLE_PREFIX . "_options");

        };

    } catch(Exception $e) {

        $this->render('@Uss/db.error.html.twig', [
            'error' => $e->getMessage(),
            'ussInstance' => $this
        ]);

        die;

    }

} else {

    $this->mysqli = $this->options = null;

}
