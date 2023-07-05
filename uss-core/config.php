<?php
/**
 * User Synthetics Configuration file
 *
 * @author ucscode <uche23mail@gmail.com>
 * @version 2
 * @license GNU-v3
 */

/**
 * Get Global Constants
 */
require_once __DIR__ . "/constants.php";

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
$class = [

    "internal" => [
        "Core.php",
        "Events.php",
        "SQuery.php",
        "Pairs.php",
        "Menufy.php",
        "DOMTable.php",
        "DataMemo.php",
        "X2Client/X2Client.php"
    ],

    "external" => [
        "Parsedown.php",
        "ParsedownExtra.php"
    ]

];

foreach($class as $directory => $filelist ) {

    # Require internal & external classes

    foreach( $filelist as $filename ) {

        require CLASS_DIR . "/{$directory}/{$filename}";

    };

};

/**
 * Incase of libraries that were required using composer, 
 * The vendor/autoload.php file will be loaded
 */
if( is_file( CLASS_DIR . "/vendor/autoload.php") ) {

    require_once CLASS_DIR . "/vendor/autoload.php";

}

/**
 * Declare Project Files
 */
$projectFiles = array(
    "conn.php",
    "uss.php",
    "modules.php"
);

/**
 * Load Project Files
 */
foreach( $projectFiles as $filename ) {

    # Welcome to user synthetics

    require_once CORE_DIR . "/{$filename}";

};
