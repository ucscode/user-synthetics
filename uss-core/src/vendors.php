<?php
/**
 * vendors.php - Composer Autoloader
 *
 * This file checks for the existence of the Composer autoloader and includes it.
 * If the autoloader is not found, it displays a vendor error and exits the script.
 *
 * @package Ucscode\Uss
 */

// Path to the Composer autoloader
$vendorAutoLoader = ROOT_DIR . "/vendor/autoload.php";

// Check if the autoloader file exists
if (!is_file($vendorAutoLoader)) {
    // Display a vendor error and exit if the autoloader is missing
    $vendorError = require_once UssImmutable::VIEW_DIR . "/vendor-error.php";
    exit($vendorError);
}

// Include the Composer autoloader
require_once $vendorAutoLoader;
