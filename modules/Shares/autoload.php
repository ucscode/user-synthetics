<?php

spl_autoload_register(function ($fqcn) {
    $fqcn = explode("\\", $fqcn);
    array_shift($fqcn);
    $classFile = __DIR__ . "/resource/" . implode("/", $fqcn) . ".php";
    require_once $classFile;
});
