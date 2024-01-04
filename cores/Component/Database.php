<?php

namespace Uss\Component;

if(in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1', '::1'], true)) {

    // Localhost Development Environment Only

    final class Database {
        const ENABLED = true;
        const PREFIX = 'uss_';
        const HOST = 'localhost';
        const USERNAME = 'root';
        const PASSWORD = '12345678';
        const NAME = 'www_uss';
        const PORT = 3306;
    };

} else {

    final class Database {
        const ENABLED = true;
        const PREFIX = 'uss_';
        const HOST = 'localhost';
        const USERNAME = '';
        const PASSWORD = '';
        const NAME = '';
        const PORT = null;
    }

};
