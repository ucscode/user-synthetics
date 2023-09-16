<?php

use Ucscode\Packages\Events;

defined('ROOT_DIR') && defined("UssEnum::MOD_DIR") || die('Illegal Access to Module Loader');

/**
 * Load Modules
 * Iterate over the contents of this directory and include the `index.php` file for each module
 */
$directories = iterator_to_array(new FileSystemIterator(UssEnum::MOD_DIR));

/** sort ascending */
usort($directories, function ($a, $b) {
    return strnatcasecmp($a->getFilename(), $b->getFilename());
});

foreach($directories as $sysIter) {

    /**
     * Prevent variable conflicts with other modules
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

        # Get the `index.php` file;
        $moduleIndex = $sysIter->getPathname() . "/index.php";

        # Require the index.php file only if it exists;
        if(file_exists($moduleIndex)) {
            require_once $moduleIndex;
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
Events::instance()->exec("modules:loaded");
