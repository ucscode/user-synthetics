<?php

namespace Ucscode\DOMTable\Interface;

interface DOMTableIteratorInterface
{
    public function foreachItem(array $item): ?array;
}
