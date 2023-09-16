<?php

use Ucscode\Packages\Pairs;

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

if(DB_ENABLED) {

    $error = null;

    try {

        $this->mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        if($this->mysqli->connect_errno) {

            $error = $this->mysqli->connect_error;

        } else {

            $this->options = new Pairs($this->mysqli, DB_PREFIX . "options");

        };

    } catch(Exception $e) {

        $error = $e->getMessage();

    };

    if($error) {

        $this->render('@Uss/db.error.html.twig', [
            'error' => $error,
            'url' => UssEnum::GITHUB_REPO,
            'mail' => UssEnum::AUTHOR_EMAIL
        ]);

        die();

    }

} else {

    $this->mysqli = $this->options = null;

}
