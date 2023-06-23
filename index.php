<?php 
/**
 * This file loads the database config, Uss class and Modules
 * @package Uss
 */
require __DIR__ . '/uss-config.php';

/*
 * In the absence of routing, a 404 error page will be rendered
 * The display of 404 error page is carried out by the index page!
*/

if( is_null(Uss::getRoute()) && $_SERVER['REQUEST_METHOD'] == 'GET' ) {
	Uss::view(function() {
		require VIEW_DIR . '/error-404.php';
	});
};

// close database connection;

if( Uss::$global['mysqli'] instanceOf MYSQLI ) Uss::$global['mysqli']->close();

