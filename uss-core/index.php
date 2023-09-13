<?php
/**
 * @package UserSynthetics
 * @author ucscode <uche23mail@gmail.com>
 * @license GNU-v3
 */

define('ROOT_DIR', realpath(__DIR__ . "/../"));

// Uss Enumerations
require_once __DIR__ . "/UssEnum.php";

// Composer Autoload
require_once UssEnum::CORE_DIR . "/vendors.php";

// Database Connection
require_once UssEnum::CORE_DIR . "/UssDB.php";

// Local Classes
require_once UssEnum::CORE_DIR . "/load-src.php";

// Uss instance
require_once UssEnum::CORE_DIR . "/Uss.php";

# Modules Loader
require_once UssEnum::CORE_DIR . "/modules.php";

// =========================================

if(empty(Uss::instance()->getRouteInventory(true))) {
    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        Uss::instance()->render('@Uss/error.html.twig');
    }
};
