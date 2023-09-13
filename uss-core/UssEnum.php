<?php

final class UssEnum {
    
    /**
     * Please change the secret key for security purpose
     * You can use a custom key or generate a random secret key using the function below
     *
     * bin2hex(random_bytes(16))
     */
    const SECRET_KEY = 'any_secret_key'; 
    
    const PROJECT_NAME = 'User Synthetics';
    
    const GITHUB_REPO = 'https://github.com/ucscode/user-synthetics';

    const AUTHOR = 'Ucscode';

    const AUTHOR_EMAIL = 'mailto:uche23mail@gmail.com';

    const AUTHOR_WEBSITE = 'https://ucscode.me';

    const MIN_PHP_VERSION = '8.1';

    const CORE_DIR = ROOT_DIR . "/uss-core";

    const MOD_DIR = ROOT_DIR . '/uss-modules';

    const ASSETS_DIR = self::CORE_DIR . '/assets';

    const VIEW_DIR = self::CORE_DIR . '/view';

    const SRC_DIR = self::CORE_DIR . "/src";

    const CONFIG_DIR = self::CORE_DIR . "/config";

    const EVENT_ID = '_';

}