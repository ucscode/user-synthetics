<?php

namespace Ucscode\Uss;

use Dotenv\Dotenv;
use Uss\Component\Kernel\Uss;

new class()
{
    public function __construct()
    {    
        $this->loadEnvironmentalVariables();
        Uss::instance(TRUE);
    }

    protected function loadEnvironmentalVariables(): void
    {
        # Load environmental variable
        $dotenv = Dotenv::createMutable(ROOT_DIR);

        if(file_exists(ROOT_DIR .'/.env.local')) {
            !file_exists(ROOT_DIR . '/.env') ?: $dotenv->load(); // load .env
            $dotenv = Dotenv::createMutable(ROOT_DIR, '.env.local');
        }

        # load .env or override with .env.local 
        $dotenv->load(); 
        
        // $dotenv->required('APP_NAME')->notEmpty();
        $dotenv->required('APP_SECRET')->notEmpty();
        $dotenv->ifPresent('APP_DEBUG')->isBoolean();
        $dotenv->required('DB_ENABLED')->isBoolean();
    }
};