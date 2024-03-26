<?php

namespace Uss\Component\Common;

class DataContainer
{
    protected $data = [];

    public function set(string $key, mixed $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function get($key): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function remove($key): self
    {
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
        }
        return $this;
    }

    public function getAll(): array
    {
        return $this->data;
    }
}