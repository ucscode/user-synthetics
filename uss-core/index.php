<?php
/**
 * This file loads the database config, Uss class and Modules
 * @author ucscode <uche23mail@gmail.com>
 * @license GNU-v3
 */

# Uss Constants
require_once __DIR__ . "/constants.php";
# Composer Autoload
require_once CORE_DIR . "/load-vendors.php";
# Database Connection
require_once CORE_DIR . "/conn.php";
# Local Classes
require_once CORE_DIR . "/load-src.php";
# Uss instance
require_once CORE_DIR . "/Uss.php";
# Modules Loader
require_once CORE_DIR . "/load-modules.php";

// =========================================

if(empty(Uss::instance()->getRouteInventory(true))) {
    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        Uss::instance()->render('@Uss/error.html.twig');
    }
};

# Close Database Connection (IF EXISTS)
if(Uss::instance()->mysqli instanceof MYSQLI) {
    Uss::instance()->mysqli->close();
}
