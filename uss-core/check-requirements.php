<?php 

defined('ROOT_DIR') || DIE;

// If version is lower than the specified php version, exit the script!

if((float)PHP_VERSION < MIN_PHP_VERSION) {
    require VIEW_DIR . '/PHP-Version.php';
    exit;
};