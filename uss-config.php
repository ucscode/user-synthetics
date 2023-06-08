<?php
/**
 * User Synthetics Configuration file
 *
 * @author ucscode <uche23mail@gmail.com>
 * @version 2.0
 * @copyright Copyright (c) 2023 ucscode 
 * @package uss
 */

/** 
 * The project name
 * 
 * @ignore
 */
define( "PROJECT_NAME", 'User Synthetics' );


/**
 * The installation directory
 * 
 * `ROOT_DIR` is a constant that defines the directory where the user synthetics project is installed
 * 
 * @var string
 */
define( "ROOT_DIR", __DIR__ );


/**
 * The display directory
 * 
 * `VIEW_DIR` defines the directory that contain files responsible for output or display of contents in user synthetics
 * 
 * @var string
 */
define( "VIEW_DIR", ROOT_DIR . '/uss-view' );


/**
 * The resource & third party directory
 * 
 * `ASSETS_DIR` contains HTML, CSS and JS files as well as popular third party libraries used by user synthetics (e.g Bootstrap, Animate.css etc)
 * 
 * @var string
 */
define( "ASSETS_DIR", ROOT_DIR . '/uss-assets' );


/**
 * The Modules Directory
 * 
 * `MOD_DIR` &mdash; This constant defines the directory that contain modules created by developers to modify the functions, look &amp; feel of user synthetics
 * 
 * @var string
 */
define( "MOD_DIR", ROOT_DIR . '/uss-modules' );


/**
 * The class directory
 * 
 * `CLASS_DIR` &mdash; defines the directory that contain class files which empowers user synthetics
 */
define( "CLASS_DIR", ROOT_DIR . '/uss-class' );


/** 
 * Verify PHP Version!
 * 
 * User Synthetics requires at least PHP 7.4 to run properly
 * @ignore
 */

define( 'MIN_PHP_VERSION', 7.4 );


// If version is lower than the specified php version, script will be exited!

if( (float)PHP_VERSION < MIN_PHP_VERSION ) {
	require VIEW_DIR . '/PHP-Version.php';
	exit;
}
	
	
/**
 * The Main Classes
 * 
 * User Synthetics requires a list of "independent" light-weight classes which are stored in the `CLASS_DIR`
 * By independent, I mean that any of the class file can be copied to another project outside user synthetics and work just fine
 */

$class = array(
	"core.php",
	"events.php",
	"sQuery.php",
	"pairs.php",
	"menufy.php",
	"DOMTable.php",
	"datamemo.php",
	"X2Client/X2Client.php",
	"vendors/Parsedown.php",
	"vendors/ParsedownExtra.php"
);

foreach( $class as $filename ) require CLASS_DIR . "/{$filename}";


/**
 * The Main User Synthetics Files
 *
 * 1. The database connection file 
 * 2. The `uss` class file
 * 3. The modules execution file
*/

require ROOT_DIR . '/uss-conn.php';
require ROOT_DIR . '/uss-class.php';
require MOD_DIR . '/index.php';