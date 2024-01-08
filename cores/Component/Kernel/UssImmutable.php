<?php

namespace Uss\Component\Kernel;

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
    public const NAMESPACE = '__uss';
    public const SESSION_KEY = 'UssId'; // (PHP Session)
    public const CLIENT_KEY = 'uss_client_id'; // (Browser Cookie)
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
    public const CORE_DIR = ROOT_DIR . "/cores";
    public const MODULES_DIR = ROOT_DIR . '/modules';
    public const ASSETS_DIR = self::CORE_DIR . '/assets';
    public const TEMPLATES_DIR = self::CORE_DIR . '/templates';
    public const JSON_DIR = self::ASSETS_DIR . '/JSON';
}
