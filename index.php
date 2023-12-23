<?php

namespace Ucscode\Uss;

use Uss\Component\Kernel\UssImmutable;

define('ROOT_DIR', __DIR__);
define('INSTALLATION_PATH', str_replace($_SERVER['DOCUMENT_ROOT'], '', ROOT_DIR));

call_user_func(function () {
    $autoloader = __DIR__ . "/vendor/autoload.php";
    if (!is_file($autoloader)) {
        die(require_once ROOT_DIR . "/uss-core/help.php");
    }
    require_once $autoloader;
});

require_once UssImmutable::CORE_DIR . '/modules.php';
