<?php
/**
 * In PHP Class, Only public properties can be accessed externally
 * This triat allows PROTECTED properties to be accessed externally but not PRIVATE properties
 *
 * What's the Idea?
 *
 * PUBLIC Properties can be accessed and also overwritten.
 * PROTECTED and PRIVATE properties cannot be accessed at all
 *
 * The idea is to ensure that PROTECTED properties can be accessed but can never be overwritten
 * While private properties can never be accessed
 */
trait ProtectedPropertyAccessTrait
{
    /**
    * Access Protected Properties
    * Trying to access private properties will throw an exception
    */
    public function __get($property)
    {
        if(!property_exists($this, $property)) {
            $error = "Undefined property: " . $this::class . "::\${$property}";
        } else {
            $value = $this->{$property} ?? null;
            if(!is_null($value)) {
                if((new ReflectionProperty($this, $property))->isPrivate()) {
                    $error = "Cannot access private property " . $this::class . "::\${$property}";
                };
            }
        };
        if(!empty($error)) {
            throw new Exception($error);
        };
        return $value;
    }

}
