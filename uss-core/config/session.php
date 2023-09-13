<?php

defined('UssEnum::CONFIG_DIR') || die('Invalid Session Access');

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

/** 
 * Create a Unique Session ID 
 */
$sidIndex = 'USSID';

if(empty($_SESSION[$sidIndex])) {

    $_SESSION[$sidIndex] = Core::keygen(40, true);

};

/**
 * Unique Device ID
 */
$cookieIndex = 'USSCLIENTID';

if(empty($_COOKIE[$cookieIndex])) {

    // Available For 3 Months
    $time = (new \DateTime())->add((new \DateInterval("P3M")));

    // Predefined Cookie
    $_COOKIE[$cookieIndex] = uniqid(Core::keygen(7));

    // Set Cookie Value
    $setCookie = setrawcookie($cookieIndex, $_COOKIE[$cookieIndex], $time->getTimestamp(), '/');

};