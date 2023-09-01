<?php

defined('ROOT_DIR') || DIE;

/**
 * Import libraries that were required using composer, 
 * I.E Through the vendor/autoload.php file
 */
$vendorLoader = ROOT_DIR . "/vendor/autoload.php";

if( !is_file($vendorLoader) ) {

    # Create Message;
    $message = [
        "<h2>" . strtoupper(PROJECT_NAME . " INITIALIZATION FAILED:" . "</h2>"),
        "- Install composer if you don't already have it",
        "- Open your <code>Terminal</code> OR <code>Command Prompt (CMD)</code>",
        "- Go to the project installation directory as shown below:",
        "<div %1\$s><code>cd %2\$s</code></div>",
        "- Then run the following command:",
        "<div %1\$s><code>composer install</code></div>"
    ];

    # Compile Message
    $message = "<div>" . implode("<div/>\n<div style='margin-top: 12px;'>", $message) . "</div>";

    # Exit script with message;
    exit(sprintf(
        $message,
        "style='border: 1px solid #555555; padding: 12px; background-color: #555555; color: white;'",
        ROOT_DIR
    ));
};

require_once $vendorLoader;
