<?php

namespace Uss\Component\Common;

use Uss\Component\Kernel\Uss;
use Uss\Component\Trait\SingletonTrait;

class AppStore
{
    use SingletonTrait;

    protected array $storage = [];

    public function set(string $name, mixed $value): self
    {
        $this->storage[$name] = $value;
        return $this;
    }

    public function add(string $name, mixed $value, ?string $key = null): static
    {
        $entity = $this->get($name) ?? [];
        if(is_array($entity)) {
            $key !== null ?
                $entity[$key] = $value :
                (!in_array($value, $entity, true) ? $entity[] = $value : null);
            $this->set($name, $entity);
        }
        return $this;
    }

    public function get(string $name): mixed
    {
        return $this->storage[$name] ?? null;
    }

    public function remove(string $name): static
    {
        if(array_key_exists($name, $this->storage)) {
            unset($this->storage[$name]);
        }
        return $this;
    }

    public function __debugInfo()
    {
        return Uss::instance()->array_map_recursive(function($item) {
            return is_object($item) ? sprintf('object(%s) {}', $item::class) : $item;
        }, $this->storage);
    }
}