<?php

/**
 * Prevent direct access to this file
 * Ensure that it is loaded only by user synthetics system
 */
defined("ROOT_DIR") or die('GREAT! &mdash; GLAD TO SEE YOU ACCESS THIS PAGE ILLEGALLY!');

/**
 * Load Modules
 * Iterate over the contents of this directory and include the `index.php` file for each module
 */
$directories = iterator_to_array(new FileSystemIterator(__DIR__));

/** sort ascending */
usort($directories, function ($a, $b) {
    return strnatcasecmp($a->getFilename(), $b->getFilename());
});

foreach($directories as $sysIter) {

    /**
     * Prevent conflicts with module variables
     *
     * Variables defined within a module automatically become global unless enclosed in a local function.
     * This can lead to conflict when multiple modules overwrite each other's variables.
     * To prevent this, each module is executed within its own dedicated local function.
     */
    call_user_func(function () use ($sysIter) {

        /**
         * Check if the content is a folder;
         * if not, skip it.
         */
        if(!$sysIter->isDir()) {
            return;
        }

        // Get the `index.php` file;

        $modIndex = $sysIter->getPathname() . "/index.php";

        // Require the index.php file only if it exists;

        if(file_exists($modIndex)) {
            require_once $modIndex;
        }

    });

};

/**
 * Modules Loaded
 *
 * Once all modules have been loaded, the "modules-loaded" event is triggered immediately.
 * Therefore, modules that rely on features of other modules should add a listener to the "modules-loaded" event.
 * This ensures that all features of the parent module are available for use before execution.
 */
Events::exec("modules-loaded");
