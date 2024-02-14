<?php

namespace Ucscode\Uss;

require_once __DIR__ . "/constants.php";

if(!is_file(VENDOR_DIR . "/autoload.php")) {
    die(require_once CORES_DIR . "/help.php");
}

require_once VENDOR_DIR . "/autoload.php";
require_once CORES_DIR . "/init.php";
require_once CORES_DIR . "/modules.php";