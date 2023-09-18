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

    try {

        $this->mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        if($this->mysqli->connect_errno) {

            throw new \Exception($this->mysqli->connect_error);

        } else {

            try {

                $this->options = new Pairs($this->mysqli, DB_PREFIX . "options");

            } catch(\Exception $e) {

                $this->render('@Uss/error.html.twig', [
                    'subject' => "Library Error",
                    'message' => $e->getMessage()
                ]);

                die();
            }
            
        }

    } catch(\Exception $e) {
        
        $error = $e->getMessage();

        $this->render('@Uss/db.error.html.twig', [
            'error' => $error,
            'url' => UssEnum::GITHUB_REPO,
            'mail' => UssEnum::AUTHOR_EMAIL
        ]);

    };

} else {

    $this->mysqli = $this->options = null;

}
