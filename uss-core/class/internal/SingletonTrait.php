<?php

/**
 * @ignore
 */
trait SingletonTrait {

    /**
     * Hold the instance of the current class 
     */
    private static $instance;

    /**
     * Singleton
     *
     * Uss class becomes an Object that can only be instantiated once but accessible globally
     * @ignore
     * @return self
     */
    public static function instance() {
        if(self::$instance === null) {
            self::$instance = new self();
        };
        return self::$instance;
    }

    /**
     * Class Constructor
     *
     * This should be updated within the class that inherits this trait
     */
    protected function __construct() {}
    
}