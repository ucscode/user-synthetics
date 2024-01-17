<?php

namespace Ucscode\DOMTable\Interface;

use mysqli_result;
use Ucscode\UssElement\UssElement;

interface DOMTableKernelInterface extends DOMTableRepositoryInterface
{
    public function setData(array|mysqli_result $iterable, ?DOMTableIteratorInterface $iterator): self;
    public function build(): UssElement;
}