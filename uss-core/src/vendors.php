<?php

defined('ROOT_DIR') || die('Illegal Vendor Compiler Access');

// Require the compose autoload.php file

$vendorLoader = ROOT_DIR . "/vendor/autoload.php";

if(!is_file($vendorLoader)) {

    // If the file is missing, display error and die

    $vendorError = require_once UssEnum::VIEW_DIR . "/vendor-error.php";

    exit($vendorError);

};

require_once $vendorLoader;
