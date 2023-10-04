<?php

use Ucscode\Event\Event;

defined('ROOT_DIR') || die('Illegal Access to Module Loader');

// Get modules directory as array
$directories = iterator_to_array(new FileSystemIterator(UssEnum::MOD_DIR));

// Sort ascending
usort($directories, function ($a, $b) {
    return strnatcasecmp($a->getFilename(), $b->getFilename());
});

foreach($directories as $sysIter) {
    $indexFile = $sysIter->getPathname() . "/index.php";
    if($sysIter->isDir() && file_exists($indexFile)) {
        call_user_func(function () use ($indexFile) {
            include_once $indexFile;
        });
    }
};

// Load Modules
Event::instance()->dispatch("Modules:loaded");

// Render 404 Error
$matchingRoutes = Uss::instance()->getRouteInventory(true);

if(empty($matchingRoutes)) {
    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        Uss::instance()->render('@Uss/error.html.twig');
    }
}
