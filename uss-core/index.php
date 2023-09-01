<?php
/**
 * This file loads the database config, Uss class and Modules
 * @author ucscode <uche23mail@gmail.com>
 * @license GNU-v3
 */
require_once __DIR__ . "/constants.php";

# require_once __DIR__ . "/check-requirements.php"; 
require_once CORE_DIR . "/load-vendors.php";
require_once CORE_DIR . "/conn.php";
require_once CORE_DIR . "/load-classes.php";
require_once CORE_DIR . "/Uss.php";
require_once CORE_DIR . "/load-modules.php";

// =========================================

/**
 * In the absence of routing, a 404 error page will be rendered
 * The display of 404 error page is carried out by the index page!
*/
if(empty(Uss::instance()->getRouteInventory(true)) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    Uss::instance()->render('@Uss/error_404.html.twig');
};

# close database connection;
if(Uss::instance()->global['mysqli'] instanceof MYSQLI) {
    Uss::instance()->global['mysqli']->close();
}
