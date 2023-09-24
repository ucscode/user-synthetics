<?php

final class UssEnum
{
    public const SECRET_KEY = 'any_secret_key';

    public const PROJECT_NAME = 'User Synthetics';

    public const GITHUB_REPO = 'https://github.com/ucscode/user-synthetics';

    public const AUTHOR = 'Ucscode';

    public const AUTHOR_EMAIL = 'uche23mail@gmail.com';

    public const AUTHOR_WEBSITE = 'https://ucscode.me';

    public const MIN_PHP_VERSION = '8.1';

    public const CORE_DIR = ROOT_DIR . "/uss-core";

    public const MOD_DIR = ROOT_DIR . '/uss-modules';

    public const ASSETS_DIR = self::CORE_DIR . '/assets';

    public const VIEW_DIR = self::CORE_DIR . '/view';

    public const SRC_DIR = self::CORE_DIR . "/src";

    public const CONFIG_DIR = self::CORE_DIR . "/config";

    public const BUILD_PATH = self::SRC_DIR . "/kernel/build";
}
