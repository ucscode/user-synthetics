<?php

namespace Ucscode\Uss;

use Dotenv\Dotenv;
use Uss\Component\Kernel\Uss;
use Uss\Component\Kernel\UssImmutable;

define('ROOT_DIR', __DIR__);
define('INSTALLATION_PATH', str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace(DIRECTORY_SEPARATOR, '/', ROOT_DIR)));

call_user_func(function () {
    $autoloader = __DIR__ . "/vendor/autoload.php";
    if (!is_file($autoloader)) {
        die(require_once __DIR__ . "/cores/help.php");
    }
    require_once $autoloader;
});

# Load environmental variable
$dotenv = Dotenv::createMutable(ROOT_DIR);
$dotenv->load();

if(file_exists(ROOT_DIR .'/.env.local')) {
    $dotenv = Dotenv::createMutable(ROOT_DIR, '.env.local');
    $dotenv->load(); // load .env.local and override existing variables
}

# Instantiate First Time With Database & Other Properties;
Uss::instance(TRUE);

require_once UssImmutable::CORE_DIR . '/Modules.php';
