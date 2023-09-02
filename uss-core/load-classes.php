<?php

defined('ROOT_DIR') || DIE;

/**
 * The Main Classes
 * Retrieve built-in and external Classes, Enum or Traits that are not managed by composer
 */
$dependencies = [
    "internal" => [
        "SingletonTrait.php",
        "Core.php",
        "Events.php",
        "SQuery.php",
        "Pairs.php",
        "Menufy.php",
        "DOMTable.php",
        "DataMemo.php",
        "X2Client/X2Client.php",
        "UssTwigBlockManager.php"
    ],
    "external" => []
];

# Include libraries in project
foreach($dependencies as $directory => $filelist ) {
    foreach( $filelist as $filename ) {
        require CLASS_DIR . "/{$directory}/{$filename}";
    };
};
