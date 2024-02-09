<?php

namespace Ucscode\Uss;

use Dotenv\Dotenv;
use Uss\Component\Kernel\Uss;
use Uss\Component\Kernel\UssImmutable;

defined('ROOT_DIR') ?: define('ROOT_DIR', __DIR__);

call_user_func(function () {
    $autoloader = __DIR__ . "/vendor/autoload.php";
    if (!is_file($autoloader)) {
        die(require_once __DIR__ . "/cores/help.php");
    }
    require_once $autoloader;
});

# Load environmental variable
$dotenv = Dotenv::createMutable(ROOT_DIR);

if(file_exists(ROOT_DIR .'/.env.local')) {
    file_exists(ROOT_DIR . '/.env') ? $dotenv->load() : null; // load .env
    $dotenv = Dotenv::createMutable(ROOT_DIR, '.env.local');
}

# load .env or override with .env.local 
$dotenv->load(); 

# Define global constants
defined('ENV_DB_PREFIX') ?: define('ENV_DB_PREFIX', $_ENV['DB_PREFIX']);

defined('INSTALLATION_PATH') ?: define(
    'INSTALLATION_PATH', 
    str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace(DIRECTORY_SEPARATOR, '/', ROOT_DIR))
);

# Instantiate First Time With Database & Other Properties;
Uss::instance(TRUE);

# Load Modules
require_once UssImmutable::CORE_DIR . '/Modules.php';
