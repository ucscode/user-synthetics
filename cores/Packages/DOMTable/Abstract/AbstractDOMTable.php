<?php

namespace Ucscode\DOMTable\Abstract;

use Ucscode\DOMTable\Interface\DOMTableIteratorInterface;
use Ucscode\DOMTable\Interface\DOMTableKernelInterface;

abstract class AbstractDOMTable extends AbstractDOMTableFactory implements DOMTableKernelInterface
{
    public readonly string $tablename;
    protected ?DOMTableIteratorInterface $fabricator;
    protected array $result;

    public function __construct(?string $tablename = null)
    {
        $this->tablename = $tablename ?: uniqid('_');
        $this->createTableElements();
        $this->orientTableElements();
    }
}
