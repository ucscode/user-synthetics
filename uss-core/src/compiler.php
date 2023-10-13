<?php

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

        'Event' => [
        ],

        'DOMTable' => [
            'DOMTable.php'
        ],

        'DataMemo' => [
            'DataMemo.php'
        ],

        'TreeNode' => [
            'TreeNode.php'
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

    'bundles' => [

        'interface' => [
            'RouteInterface.php',
            'UssInterface.php'
        ],

        'enum' => [],

        'trait' => [
            "SingletonTrait.php",
        ],

        'abstract' => [
            "AbstractUssUtils.php",
            'AbstractUss.php',
        ],

        "class" => [
            'Route.php',
            'EventInterface.php',
            'Event.php',
            "UssTwigGlobalExtension.php",
            "UssTwigBlockManager.php",
            'Uss.php',
        ]

    ],

];


foreach($dependencies as $category => $projects) {
    foreach($projects as $container => $fileList) {
        foreach($fileList as $filename) {
            $__file = UssImmutable::SRC_DIR . "/" . $category . "/" . $container . "/" . $filename;
            require_once $__file;
        }
    }
}
