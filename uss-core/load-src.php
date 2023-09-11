<?php

defined('ROOT_DIR') || die;

/**
 * The Main Classes
 * Retrieve built-in and external Classes, Enum or Traits that are not managed by composer
 */
$dependencies = [

    /*
    'services' => [

        'UssElement' => [
            'UssElementInterface.php',
            'AbstractUssElementNodeList.php',
            'AbstractUssElementParser.php',
            'UssElement.php'
        ],

        'UssForm' => [
            'UssFormInterface.php',
            'UssForm.php'
        ],

        'Events' => [
            'Events.php'
        ],

        'DOMTable' => [
            'DOMTable.php'
        ],

        'DataMemo' => [
            'DataMemo.php'
        ],

        'FamilyTree' => [
            'FamilyTree.php'
        ],

        'Pairs' => [
            'Pairs.php'
        ],

        'SQuery' => [
            'SQuery.php'
        ],

        'X2Client' => [
            'Translator.php',
            'X2Client.php'
        ]

    ],

    */
    'components' => [

        'interface' => [],

        'enum' => [],

        'trait' => [
            "SingletonTrait.php",
            "EncapsulatedPropertyAccessTrait.php",
        ],

        'abstract' => [],

        "class" => [
            "UssTwigBlockManager.php",
            "Core.php",
            "SQuery.php",
            "Pairs.php",
            "Events.php"
        ]

    ]

];

# components, services:
foreach($dependencies as $directory => $content) {

    # UssElement, UssForm, Interface...
    foreach($content as $path => $includes) {
        
        # ".php" files
        foreach($includes as $filename) {

            require SRC_DIR . "/{$directory}/{$path}/{$filename}";

        }

    };

};
