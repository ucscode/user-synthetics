<?php

define('ROOT_DIR', realpath(__DIR__ . "/../"));

require_once __DIR__ . "/UssDB.php";
require_once __DIR__ . "/src/UssImmutable.php";

require_once UssImmutable::SRC_DIR . "/vendors.php";
require_once UssImmutable::SRC_DIR . "/compiler.php";
require_once UssImmutable::SRC_DIR . "/modules.php";
