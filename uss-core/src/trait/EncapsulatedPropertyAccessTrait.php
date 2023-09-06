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
trait EncapsulatedPropertyAccessTrait
{
    /**
    * Access Protected Properties
    * Trying to access private properties will throw an exception
    */
    public function __get($property)
    {
        if(!property_exists($this, $property)) {
            throw new Error("Undefined property: " . $this::class . "::\${$property}");
        };
        $reflectionProperty = new ReflectionProperty($this, $property);
        $attributes = $reflectionProperty->getAttributes('Accessible');
        if(empty($attributes)) {
            $scope = $reflectionProperty->isPrivate() ? 'private' : 'protected';
            throw new Error("Cannot access {$scope} property " . $this::class . "::\${$property}");
        };
        return $this->{$property} ?? null;
    }

}
