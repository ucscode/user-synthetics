<?php

defined('CONFIG_DIR') || die;

/**
* Establish a new session, create a session ID, and generate a browser unique ID.
*
* This method is responsible for initializing a new session in the User Synthetics system.
*
* It creates a session ID to identify the session and generates a unique ID specific to the user's browser.
*
* However, please note that relying solely on the browser ID for long-term usage is not recommended as clearing browser cookies can easily wipe out the browser ID and generate a new one.
*
* @return void
* @ignore
*/

if(empty(session_id())) {
    session_start();
}

/* Create a unique visitor session ID */

if(empty($_SESSION['uss_session_id']) || strlen($_SESSION['uss_session_id']) < 50) {
    $_SESSION['uss_session_id'] = Core::keygen(mt_rand(50, 80), true);
};

/* - Unique Device ID; */

if(empty($_COOKIE['ussid'])) {
    $time = (new DateTime())->add((new DateInterval("P6M")));
    $_COOKIE['ussid'] = uniqid(Core::keygen(7));
    $setCookie = setrawcookie('ussid', $_COOKIE['ussid'], $time->getTimestamp(), '/');
};
