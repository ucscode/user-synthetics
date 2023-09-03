<?php

/**
 * The project name
 * @ignore
 */
define("PROJECT_NAME", 'User Synthetics');

/**
 * The installation directory
 * Defines the directory where the user synthetics project is installed
 */
define("ROOT_DIR", realpath(__DIR__ . "/../"));

/**
 * The core directory
 * Defines the directory which contains the main components of user synthetics
 */
define("CORE_DIR", ROOT_DIR . "/uss-core");

/**
 * The Modules Directory
 * Defines the directory that contain modules created by developers to modify functionalities and appearance in user synthetics
 */
define("MOD_DIR", ROOT_DIR . '/uss-modules');

/**
 * The resource & third party directory
 * Defines the directory which contains front-end scripts and libraries used by user synthetics
 */
define("ASSETS_DIR", CORE_DIR . '/assets');

/**
 * The display directory
 * Defines the directory that contain files responsible for output in user synthetics
 */
define("VIEW_DIR", CORE_DIR . '/view');

/**
 * The class directory
 * Defines the directory that contain class files which empowers user synthetics
 */
define("CLASS_DIR", CORE_DIR . '/class');

/**
 * Verify PHP Version!
 *
 * User Synthetics requires at least PHP 7.4 to run properly
 * @ignore
 */
define('MIN_PHP_VERSION', 7.4);
