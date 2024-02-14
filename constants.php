<?php

namespace Ucscode\Uss;

defined('ROOT_DIR') ?: define('ROOT_DIR', __DIR__);
defined('CORES_DIR') ?: define('CORES_DIR', ROOT_DIR . "/cores");
defined('MODULES_DIR') ?: define('MODULES_DIR', ROOT_DIR . '/modules');
defined('VENDOR_DIR') ?: define('VENDOR_DIR', ROOT_DIR . '/vendor');

defined('INSTALLATION_PATH') ?: 
define('INSTALLATION_PATH', str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace(DIRECTORY_SEPARATOR, '/', ROOT_DIR)));