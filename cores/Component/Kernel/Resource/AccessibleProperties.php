<?php

namespace Uss\Component\Kernel\Resource;

class AccessibleProperties
{
    protected array $properties;
    protected array $entityProperties;

    public function __construct(protected object $entity, array $properties)
    {
        $this->entityProperties = array_keys(get_object_vars($this->entity));
        $this->properties = array_filter($properties, fn ($property) => is_string($property));
        $this->properties = array_intersect($this->entityProperties, $this->properties);
    }

    public function __call(string $property, mixed $args): mixed
    {
        if(in_array($property, $this->properties)) {
            return $this->entity->{$property};
        }
        
        if(in_array($property, $this->entityProperties)) {
            throw new \RuntimeException(
                "Access to `{$property}` property is not allowed within twig templates."
            );
        }

        return null;
    }
}