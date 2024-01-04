<?php

namespace Ucscode\LocalStorage;

use Ucscode\LocalStorage\Abstract\AbstractLocalStorage;

class LocalStorage extends AbstractLocalStorage
{
    public function save(): bool
    {
        return !!file_put_contents($this->filepath, $this->encode(), LOCK_EX);
    }

    public function getContext(): array
    {
        return $this->storage;
    }

    public function clearAll(): void
    {
        $this->storage = [];
        return;
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }
}
