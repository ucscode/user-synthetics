<?php

defined('CONFIG_DIR') || die;

self::$globals['icon'] = Core::url(ASSETS_DIR . '/images/origin.png');

self::$globals['title'] = PROJECT_NAME;

self::$globals['tagline'] = "The excellent development tool for future oriented programmers";

self::$globals['copyright'] = ((new DateTime())->format('Y'));

self::$globals['description'] = "Ever thought of how to make your programming life easier in developing website? \nUser Synthetics offers the best solution for a simple programming lifestyle";

self::$globals['website'] = $this->projectUrl;
