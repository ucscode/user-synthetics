<?php
/**
 * index.php - Project Entry Point
 *
 * This file serves as the entry point for the project. It defines the 'ROOT_DIR'
 * constant, includes necessary files for the project, and initializes key components.
 *
 * @package Ucscode\Uss
 */

// Define the root directory for the project
define('ROOT_DIR', realpath(__DIR__ . "/../"));

// Include constant files
require_once __DIR__ . "/database.php";
require_once __DIR__ . "/src/UssImmutable.php";

// Include main components
require_once UssImmutable::SRC_DIR . "/vendors.php";
require_once UssImmutable::SRC_DIR . "/autoload.php";
require_once UssImmutable::SRC_DIR . "/modules.php";
