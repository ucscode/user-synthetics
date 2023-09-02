<?php

defined('ROOT_DIR') || DIE;

/**
 * The Main Classes
 * The are built-in or external classes that are not managed with composer
 */
$classResource = [
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
    "external" => [
        "Parsedown.php",
        "ParsedownExtra.php"
    ]
];

# Include libraries in project
foreach($classResource as $directory => $component ) {
    foreach( $component as $filename ) {
        require CLASS_DIR . "/{$directory}/{$filename}";
    };
};
