<?php

namespace Ucscode\LocalStorage;

use Ucscode\LocalStorage\Abstract\AbstractLocalStorage;

class LocalStorage extends AbstractLocalStorage
{
    /**
     * Compress and save the current localstorage information into the filepath
     */
    public function save(): bool
    {
        return !!file_put_contents($this->filepath, $this->encode(), LOCK_EX);
    }

    /**
     * Get all information available in the localstorage
     */
    public function getContext(): array
    {
        return $this->storage;
    }

    /**
     * Clear all information from the localstorage
     */
    public function clearAll(): void
    {
        $this->storage = [];
        return;
    }

    /**
     * Get the file path of the localstorage
     */
    public function getFilepath(): string
    {
        return $this->filepath;
    }
}
