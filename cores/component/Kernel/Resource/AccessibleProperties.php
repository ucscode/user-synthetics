<?php

namespace Uss\Component\Kernel\Resource;

class AccessibleProperties
{
    public function __construct(protected object $entity, protected array $properties)
    {
    }

    public function __call(string $property, mixed $args): mixed
    {
        $entityProperties = array_keys(get_object_vars($this->entity));

        $properties = array_intersect(
            $entityProperties, 
            array_filter($this->properties, fn ($property) => is_string($property))
        );

        if(in_array($property, $properties)) {
            return $this->entity->{$property};
        }

        if(in_array($property, $entityProperties)) {
            throw new \RuntimeException(
                sprintf(
                    "Access to `%s` property is not allowed within twig templates.",
                    $property
                )
            );
        }

        throw new \RuntimeException(
            sprintf(
                "Trying to access undefined extension property `%s`.", 
                $property
            )
        );
    }
}