<?php

/**
 * Class UssImmutable
 *
 * This class represents an immutable object, meaning that its state cannot be modified
 * after an instance is created. The use of the `final` keyword ensures that this class
 * cannot be extended or subclassed.
 *
 * @package Ucscode\Uss
 */
final class UssImmutable
{
    public const DEBUG = true;
    public const SECRET_KEY = 'any_secret_key';
    public const PROJECT_NAME = 'User Synthetics';
    public const PROJECT_WEBSITE = 'https://usersynthetics.com';
    public const GITHUB_REPO = 'https://github.com/ucscode/user-synthetics';
    public const AUTHOR = 'Ucscode';
    public const AUTHOR_EMAIL = 'uche23mail@gmail.com';
    public const AUTHOR_WEBSITE = 'https://ucscode.me';
    public const MIN_PHP_VERSION = '8.1';
    public const VENDOR_DIR = ROOT_DIR . '/vendor';
    public const CORE_DIR = ROOT_DIR . "/uss-core";
    public const MOD_DIR = ROOT_DIR . '/uss-modules';
    public const ASSETS_DIR = self::CORE_DIR . '/assets';
    public const VIEW_DIR = self::CORE_DIR . '/view';
    public const SRC_DIR = self::CORE_DIR . "/src";
    public const CONFIG_DIR = self::CORE_DIR . "/config";
    public const JSON_DIR = self::ASSETS_DIR . '/JSON';
}
