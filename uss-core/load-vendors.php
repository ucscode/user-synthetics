<?php

defined('ROOT_DIR') || die;

# Require the compose autoload.php file

$vendorLoader = ROOT_DIR . "/vendor/autoload.php";

if(!is_file($vendorLoader)) {

    # If the file is missing, display error and die

    exit(require_once VIEW_DIR . "/vendor-error.php");

};

require_once $vendorLoader;
