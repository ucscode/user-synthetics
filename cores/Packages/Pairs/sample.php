<?php

namespace Ucscode\Pairs;

use mysqli;

spl_autoload_register(function ($classname) {
    $path = str_replace("\\", "/", $classname);
    $basename = basename($path);
    $dir = basename(dirname($path));
    require_once dirname(__DIR__) . "/{$dir}/{$basename}.php";
});

$mysqli = new mysqli('localhost', 'root', '12345678', 'www_uss');

$pairs = new Pairs($mysqli, 'pairs_sample');

$constraint = (new ForeignConstraint('uss_users'))
    ->setPrimaryKeyUnsigned(true);

$pairs->setForeignConstraint($constraint);

var_dump($pairs->remove("balance", 1));