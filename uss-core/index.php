<?php
/**
 * User Synthetics Framework
 *
 * A versatile PHP framework designed for the seamless development of innovative projects.
 *
 * Combining the flexibility of a framework and the feature-rich capabilities of a CMS, User Synthetic empowers
 * developers to build and customize applications efficiently. With a focus on simplicity and extensibility,
 * it provides a foundation for creating diverse web applications and platforms.
 *
 * @category   Framework
 * @package    Ucscode\Uss
 * @author     Uchenna Ajah
 * @license    MIT License
 * @link       https://github.com/ucscode/user-synthetics
 * @version    4.1.0
 *
 * Requirements:
 * - PHP 8.1+
 * - Composer for dependency management
 *
 * Directory Structure:
 * - /uss-core: Contains the project source code
 * - /uss-modules: Contains modules for platform expansion
 * - /vendor: Composer dependencies
 *
 * @see https://github.com/ucscode/user-synthetics
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
