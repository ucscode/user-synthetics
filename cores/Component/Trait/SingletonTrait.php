<?php

namespace Uss\Component\Trait;

trait SingletonTrait
{
    private static $instance;

    public static function instance(...$args)
    {
        if(is_null(self::$instance)) {
            self::$instance = new self(...$args);
        };
        return self::$instance;
    }

    protected function __construct(...$args)
    {
        $parent = get_parent_class($this);
        if($parent && method_exists($parent, '__construct')) {
            parent::__construct(...$args);
        }
    }

}
