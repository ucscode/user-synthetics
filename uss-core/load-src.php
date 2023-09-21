<?php

defined('ROOT_DIR') || die;

/**
 * The Main Classes
 * Retrieve built-in and external Classes, Enum or Traits that are not managed by composer
 */
$dependencies = [

    'packages' => [

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
            'SQueryInterface.php',
            'SQueryTrait.php',
            'AbstractSQuery.php',
            'SQuery.php'
        ],

        'X2Client' => [
            'Translator.php',
            'X2Client.php'
        ]

    ],

    'components' => [

        'interface' => [],

        'enum' => [],

        'trait' => [
            "SingletonTrait.php",
            "PropertyAccessTrait.php",
        ],

        'abstract' => [],

        "class" => [
            "UssTwigBlockManager.php",
            "Core.php",
        ]

    ]

];

// components, services:
foreach($dependencies as $directory => $container) {

    // UssElement, UssForm, Interface...
    foreach($container as $path => $array) {

        // List of PHP Files
        foreach($array as $filename) {

            // Include PHP Files
            require_once UssEnum::SRC_DIR . "/" . $directory . "/" . $path . "/" . $filename;

        }

    };

};
