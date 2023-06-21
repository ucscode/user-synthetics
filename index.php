<?php 
/**
 * User Synthetics
 *
 * This file requires the uss-config.php which is responsible for initialization, defining constants and including the core classes of user synthetics.
 *
 * The file is also responsible for the output of 404 error page not URL path is focused
 *
*/
require __DIR__ . '/uss-config.php';

/*
 * Displaying 404 Error page!
 *
 * 404 error page in user synthetics in not really an error. However, the 404 error page will be displayed on screen if:
 * - No `Uss::view()` method was previously called
 * - No `Uss::focus()` method is pointing to the current URL Query
 * - No module has called on `exit()` or `die()` function
 *
 * The display of 404 error page is carried out by the index page!
*/

if( is_null(Uss::getFocus()) && $_SERVER['REQUEST_METHOD'] == 'GET' ) {
	Uss::view(function() {
		require VIEW_DIR . '/error-404.php';
	});
};

// close database connection;

if( Uss::$global['mysqli'] instanceOf MYSQLI ) Uss::$global['mysqli']->close();

