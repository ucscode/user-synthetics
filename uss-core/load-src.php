<?php

defined('ROOT_DIR') || die;

/**
 * The Main Classes
 * Retrieve built-in and external Classes, Enum or Traits that are not managed by composer
 */
$dependencies = [

    'interface' => [
        "UssElementInterface.php",
        "UssFormInterface.php",
    ],

    'trait' => [
        "SingletonTrait.php",
        "EncapsulatedPropertyAccessTrait.php",
    ],

    'abstract' => [
        "AbstractUssElementNodeList.php",
        "AbstractUssElementParser.php",
    ],

    "class" => [
        "UssTwigBlockManager.php",
        "UssElementBuilder.php",
        "UssForm.php",
        "Core.php",
        "Events.php",
        "SQuery.php",
        "Pairs.php",
        "Menufy.php",
        "DOMTable.php",
        "DataMemo.php",
        "X2Client/X2Client.php"
    ]

];

# Include libraries in project

foreach($dependencies as $path => $filelist) {
    foreach($filelist as $filename) {
        require SRC_DIR . "/{$path}/{$filename}";
    };
};
