<?php

trait SingletonTrait
{
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
    public static function instance(...$args)
    {
        if(self::$instance === null) {
            self::$instance = new self(...$args);
        };
        return self::$instance;
    }

    /**
     * Class Constructor
     *
     * This should be updated within the class that inherits this trait
     */
    protected function __construct(...$args)
    {
        $parent = get_parent_class($this);
        if($parent && method_exists($parent, '__construct')) {
            parent::__construct(...$args);
        }
    }

}
