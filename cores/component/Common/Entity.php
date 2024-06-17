<?php

namespace Uss\Component\Common;

use ArrayAccess;

class Entity implements ArrayAccess
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

    public function overwrite(array $properties): self
    {
        $this->properties = $properties;

        return $this;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->properties[$offset] = $value;
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->properties[$offset] ?? null;
    }

    public function offsetUnset(mixed $offset): void
    {
        if(array_key_exists($offset, $this->properties)) {
            unset($this->properties[$offset]);
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return !empty($this->properties[$offset]);
    }
}