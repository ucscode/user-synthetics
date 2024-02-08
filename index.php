<?php

namespace Ucscode\Uss;

use Uss\Component\Kernel\Uss;
use Uss\Component\Kernel\UssImmutable;

define('ROOT_DIR', __DIR__);
define('INSTALLATION_PATH', str_replace($_SERVER['DOCUMENT_ROOT'], '', ROOT_DIR));
var_dump($_SERVER['DOCUMENT_ROOT'], ROOT_DIR, INSTALLATION_PATH);
call_user_func(function () {
    $autoloader = __DIR__ . "/vendor/autoload.php";
    if (!is_file($autoloader)) {
        die(require_once __DIR__ . "/cores/help.php");
    }
    require_once $autoloader;
});

/**
 * Instantiate First Time With Database & Other Properties;
 */
Uss::instance(TRUE);

require_once UssImmutable::CORE_DIR . '/Modules.php';
