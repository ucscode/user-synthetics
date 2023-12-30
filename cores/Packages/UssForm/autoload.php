<?php

spl_autoload_register(function ($classname) {
    $path = explode("/", str_replace("\\", "/", $classname));
    array_shift($path);
    $basename = array_pop($path);
    $dir = implode("/", $path);
    require_once dirname(__DIR__) . "/{$dir}/{$basename}.php";
});
