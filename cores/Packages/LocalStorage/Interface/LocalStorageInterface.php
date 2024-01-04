<?php

namespace Ucscode\LocalStorage\Interface;

use ArrayAccess;

interface LocalStorageInterface
{
    public function clearAll(): void;
    public function save(): bool;
    public function getFilepath(): string;
    public function getContext(): array;
}