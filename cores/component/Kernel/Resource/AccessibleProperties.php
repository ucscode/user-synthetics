<?php

namespace Uss\Component\Kernel\Resource;

class AccessibleProperties
{
    public function __construct(protected array $properties, protected object $classInstance)
    {}

    public function __call(string $property, mixed $args): mixed
    {
        [$classInstanceProperties, $accessibleProperties] = $this->getProperties();

        if(in_array($property, $accessibleProperties)) {
            return $this->classInstance->{$property};
        }

        if(in_array($property, $classInstanceProperties)) {
            throw new \RuntimeException(
                sprintf("Access to `%s` property is not allowed within twig templates.", $property)
            );
        }

        throw new \RuntimeException(
            sprintf("Trying to access undefined extension property `%s`.", $property)
        );
    }

    protected function getProperties(): array
    {
        $availableProperties = array_keys(get_object_vars($this->classInstance));
        $accessibleProperties = array_intersect(
            $availableProperties, 
            array_filter($this->properties, fn ($property) => is_string($property))
        );
        return [$availableProperties, $accessibleProperties];
    }

    public function __debugInfo()
    {
        return $this->getProperties()[1];
    }
}