<?php

defined('CONFIG_DIR') || DIE;

$this->global['icon'] = Core::url(ASSETS_DIR . '/images/origin.png');
$this->global['title'] = PROJECT_NAME;
$this->global['tagline'] = "The excellent development tool for future oriented programmers";
$this->global['copyright'] = ((new DateTime())->format('Y'));
$this->global['description'] = "Ever thought of how to make your programming life easier in developing website? \nUser Synthetics offers the best solution for a simple programming lifestyle";
$this->global['website'] = $this->project_url;
$this->global['body.attrs'] = array("class" => 'uss');
