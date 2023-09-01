<?php
/**
 * A Global Extension for Twig On User Synthetics
 */
class UssTwig {

    private static $instance;

    public array $attr = [];

    private function __construct() {
        Uss::instance()->console('platform', PROJECT_NAME);
        $this->setAttr('console64', base64_encode(json_encode(Uss::instance()->console())));
    }

    public static function instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function setAttr(string $key, mixed $value) {
        $this->attr[$key] = $value;
    }

}