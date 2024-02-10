<?php

namespace Ucscode\Uss;

use Dotenv\Dotenv;
use Uss\Component\Kernel\Uss;
use Uss\Component\Kernel\UssImmutable;

new class()
{
    public function __construct()
    {    
        defined('ROOT_DIR') ?: define('ROOT_DIR', __DIR__);

        call_user_func(function () {
            $autoloader = __DIR__ . "/vendor/autoload.php";
            is_file($autoloader) ?: die(require_once __DIR__ . "/cores/help.php");
            require_once $autoloader;
        });

        $this->loadDotEnv();
        $this->defineGlobalConstants();
        $this->initializeUserSynthetics();
    }

    protected function loadDotEnv(): void
    {
        # Load environmental variable
        $dotenv = Dotenv::createMutable(ROOT_DIR);

        if(file_exists(ROOT_DIR .'/.env.local')) {
            file_exists(ROOT_DIR . '/.env') ? $dotenv->load() : null; // load .env
            $dotenv = Dotenv::createMutable(ROOT_DIR, '.env.local');
        }

        # load .env or override with .env.local 
        $dotenv->load(); 

        $dotenv->required('APP_NAME')->notEmpty();
        $dotenv->required('APP_SECRET')->notEmpty();
        $dotenv->ifPresent('APP_DEBUG')->isBoolean();
        $dotenv->required('DB_ENABLED')->isBoolean();

    }

    protected function defineGlobalConstants(): void
    {
        defined('INSTALLATION_PATH') ?: define(
            'INSTALLATION_PATH', 
            str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace(DIRECTORY_SEPARATOR, '/', ROOT_DIR))
        );
    }

    protected function initializeUserSynthetics(): void
    {
        # Instantiate First Time With Database & Other Properties;
        Uss::instance(TRUE);

        # Load Modules
        require_once UssImmutable::CORE_DIR . '/Modules.php';
    }
};