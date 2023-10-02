<?php

defined('ROOT_DIR') || die('@VENDOR');

$vendorAutoLoader = ROOT_DIR . "/vendor/autoload.php";

if(!is_file($vendorAutoLoader)) {

    $vendorError = require_once UssEnum::VIEW_DIR . "/vendor-error.php";

    exit($vendorError);

};

require_once $vendorAutoLoader;
