<?php

final class UssEnum
{
    /**
     * Please change the secret key for security purpose
     * You can use a custom key or generate a random secret key using the function below
     *
     * bin2hex(random_bytes(16))
     */
    public const SECRET_KEY = 'any_secret_key';

    public const PROJECT_NAME = 'User Synthetics';

    public const GITHUB_REPO = 'https://github.com/ucscode/user-synthetics';

    public const AUTHOR = 'Ucscode';

    public const AUTHOR_EMAIL = 'mailto:uche23mail@gmail.com';

    public const AUTHOR_WEBSITE = 'https://ucscode.me';

    public const MIN_PHP_VERSION = '8.1';

    public const CORE_DIR = ROOT_DIR . "/uss-core";

    public const MOD_DIR = ROOT_DIR . '/uss-modules';

    public const ASSETS_DIR = self::CORE_DIR . '/assets';

    public const VIEW_DIR = self::CORE_DIR . '/view';

    public const SRC_DIR = self::CORE_DIR . "/src";

    public const CONFIG_DIR = self::CORE_DIR . "/config";

    public const EVENT_ID = '_';

}
