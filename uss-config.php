<?php
/**
 * User Synthetics Configuration file
 *
 * @author ucscode <uche23mail@gmail.com>
 * @version 2.0
 * @license GNU-v3
 * @package uss
 */

/**
 * The project name
 * @ignore
 */
define("PROJECT_NAME", 'User Synthetics');

/**
 * The installation directory
 * Defines the directory where the user synthetics project is installed
 */
define("ROOT_DIR", __DIR__);

/**
 * The display directory
 * Defines the directory that contain files responsible for output in user synthetics
 */
define("VIEW_DIR", ROOT_DIR . '/uss-view');

/**
 * The resource & third party directory
 * Defines the directory which contains front-end scripts and libraries used by user synthetics
 */
define("ASSETS_DIR", ROOT_DIR . '/uss-assets');

/**
 * The Modules Directory
 * Defines the directory that contain modules created by developers to modify functionalities and appearance in user synthetics
 */
define("MOD_DIR", ROOT_DIR . '/uss-modules');

/**
 * The class directory
 * Defines the directory that contain class files which empowers user synthetics
 */
define("CLASS_DIR", ROOT_DIR . '/uss-class');


/**
 * Verify PHP Version!
 *
 * User Synthetics requires at least PHP 7.4 to run properly
 * @ignore
 */
define('MIN_PHP_VERSION', 7.4);


// If version is lower than the specified php version, exit the script!

if((float)PHP_VERSION < MIN_PHP_VERSION) {
    require VIEW_DIR . '/PHP-Version.php';
    exit;
};


/**
 * The Main Classes
 *
 * User Synthetics requires a list of "independent" light-weight classes which are stored in the `CLASS_DIR`
 * In order words, any of the class file can be copied to another project outside user synthetics and work just fine
 */
$class = array(
    "Core.php",
    "Events.php",
    "SQuery.php",
    "Pairs.php",
    "Menufy.php",
    "DOMTable.php",
    "DataMemo.php",
    "X2Client/X2Client.php",
    "vendors/Parsedown.php",
    "vendors/ParsedownExtra.php"
);

foreach($class as $filename) {
    require CLASS_DIR . "/{$filename}";
}


/**
 * - Config Database
 * - Include Uss Class
 * - Load Modules
 */
require ROOT_DIR . '/uss-conn.php';
require ROOT_DIR . '/uss-class.php';
require MOD_DIR . '/index.php';
