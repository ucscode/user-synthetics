<?php

namespace Uss\Component\Kernel;

/**
 * This class represents an immutable object, meaning that its state cannot be modified after an instance is created. 
 * 
 * @package Ucscode\Uss
 */
final class UssImmutable
{
    public const PROJECT_NAME = 'User Synthetics';

    public const PROJECT_WEBSITE = 'https://example.com';

    public const PROJECT_GITHUB_REPOSITORY = 'https://github.com/ucscode/user-synthetics';

    public const APP_NAMESPACE = 'Uss';

    public const APP_EXTENSION_KEY = '__uss';

    public const APP_SESSION_KEY = 'UssId'; // (PHP Session)

    public const APP_CLIENT_KEY = 'uss_client_id'; // (Browser Cookie)

    public const APP_MIN_PHP_VERSION = '8.1';

    public const AUTHOR_NAME = 'Ucscode';

    public const AUTHOR_EMAIL = 'uche23mail@gmail.com';

    public const AUTHOR_WEBSITE = 'http://ucscode.com';

    public const CORES_DIR = CORES_DIR;

    public const VENDOR_DIR = ROOT_DIR . '/vendor';

    public const MODULES_DIR = ROOT_DIR . '/modules';

    public const ASSETS_DIR = self::CORES_DIR . '/assets';

    public const TEMPLATES_DIR = self::CORES_DIR . '/templates';

    public const JSON_DIR = self::ASSETS_DIR . '/JSON';
}
