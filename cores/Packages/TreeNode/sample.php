<?php

namespace Ucscode\TreeNode;

spl_autoload_register(function ($classname) {
    $path = str_replace("\\", "/", $classname);
    $basename = basename($path);
    $dir = basename(dirname($path));
    require_once dirname(__DIR__) . "/{$dir}/{$basename}.php";
});

$parent = new TreeNode();
for($x = 0; $x < 2; $x++) {
    $node = new TreeNode(null, [
    ]);
    $parent->addChild('child-' . $x, $node);
}
$node
    ->addChild("sponge")
        ->addChild("spray")
            ->addChild("money");

var_dump($parent->findIndexChild(5));