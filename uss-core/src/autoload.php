<?php
/**
 * autoload.php - Uss Main Autoloader
 *
 * This file defines an autoloader for this project. It dynamically loads classes
 * based on the project's directory structure. The autoloader handles both the project's
 * specific namespaces and bundles, facilitating automatic class loading without the
 * need for manual includes.
 *
 * @package Ucscode\Uss
 */

/**
 * Custom autoloader function using anonymous functions and iterators.
 *
 * @return array An array of iterators representing the directories to be autoloaded.
 */
$autoloader = call_user_func(function () {
    $iterator = function (string $dir) {
        return new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                UssImmutable::SRC_DIR . '/' . $dir,
                FilesystemIterator::SKIP_DOTS
            )
        );
    };
    return [$iterator('bundles')];
});

/**
 * Spl autoload register function.
 *
 * @param string $className The fully qualified class name.
 */
spl_autoload_register(function ($className) use ($autoloader) {
    $package = explode('\\', $className);

    if(count($package) > 1) {
        $prefix = array_shift($package); // Remove first value;
        switch($prefix) {
            case 'Ucscode':
                // Search in the packages directory
                $filepath = UssImmutable::SRC_DIR . "/packages/";
                $file = implode("/", $package) . ".php";
                $filepath .= $file;
                if(is_file($filepath)) {
                    return require $filepath;
                }
                break;
        }
    };

    foreach($autoloader as $iterator) {
        foreach($iterator as $fileinfo) {
            if(strtoupper($fileinfo->getExtension()) === 'PHP') {
                $filename = $fileinfo->getBasename('.php');
                if($className === $filename) {
                    require $fileinfo->getPathname();
                    break;
                }
            };
        }
    }
});
