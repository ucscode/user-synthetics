<?php

define('ROOT_DIR', realpath(__DIR__ . '/../'));
define('INSTALLATION_PATH', str_replace($_SERVER['DOCUMENT_ROOT'], '', ROOT_DIR));

require_once realpath(__DIR__ . "/../") . "/vendor/autoload.php";
