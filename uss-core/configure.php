<?php

defined('ROOT_DIR') || DIE;

$loader = new \Twig\Loader\FilesystemLoader(VIEW_DIR);
$twigGlobal = new \Twig\Environment($loader);
