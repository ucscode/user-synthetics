<?php
/**
 * User Synthetics
 *
 * A Powerful Modular Framework For Advance Web Development
 *
 * @package    Uss
 * @version    3.0.3
 * @since      2023-May-23
 * @author     Uchenna Ajah
 * @link       http://github.com/ucscode/user-synthetics
 * @license    MIT
 */

namespace Uss;

define('ROOT_DIR', realpath(__DIR__ . "/../"));

require_once __DIR__ . "/UssDB.php";
require_once __DIR__ . "/src/UssEnum.php";

require_once UssEnum::SRC_DIR . "/vendors.php";
require_once UssEnum::SRC_DIR . "/compiler.php";
require_once UssEnum::SRC_DIR . "/modules.php";
