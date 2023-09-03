<?php

defined('CONFIG_DIR') || DIE;

self::$global['icon'] = Core::url(ASSETS_DIR . '/images/origin.png');

self::$global['title'] = PROJECT_NAME;

self::$global['tagline'] = "The excellent development tool for future oriented programmers";

self::$global['copyright'] = ((new DateTime())->format('Y'));

self::$global['description'] = "Ever thought of how to make your programming life easier in developing website? \nUser Synthetics offers the best solution for a simple programming lifestyle";

self::$global['website'] = $this->projectUrl;
