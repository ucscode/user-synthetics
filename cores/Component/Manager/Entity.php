<?php

namespace Uss\Component\Manager;

class Entity
{
    public function __construct(protected ?string $name = null, protected array $properties = [])
    {}

    public function getName(): ?string
    {
        return $this->name;
    }
    
    public function set(string $key, mixed $value): self
    {
        $this->properties[$key] = $value;
        return $this;
    }

    public function get(string $key): mixed
    {
        return $this->properties[$key] ?? null;
    }

    public function remove(string $key): self
    {
        if(array_key_exists($key, $this->properties)) {
            unset($this->properties[$key]);
        }
        return $this;
    }

    public function getAll(): array
    {
        return $this->properties;
    }
}