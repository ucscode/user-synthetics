<?php

namespace Ucscode\DOMTable;

use mysqli_result;
use Ucscode\DOMTable\Abstract\AbstractDOMTable;
use Ucscode\DOMTable\Interface\DOMTableIteratorInterface;
use Ucscode\UssElement\UssElement;

class DOMTable extends AbstractDOMTable
{
    /**
     * @param array|mysqli_result $iterable         A complete set of iterable resource
     * @param DOMTableIteratorInterface $iterator   An iterator to modify the context of each data
     */
    public function setData(array|mysqli_result $iterable, ?DOMTableIteratorInterface $iterator = null): self
    {
        $this->data = $iterable;
        $this->iteratorInterface = $iterator;
        return $this;
    }

    public function build(): UssElement
    {        
        $this->setBoundary();
        
        $this->totalItems = 0;
        $this->collections = [];
        $startIndex = ($this->currentPage - 1) * $this->itemsPerPage;

        foreach($this->getGenerator() as $index => $item) {
            $this->totalItems++;
            if($index >= $startIndex && $this->itemsInCurrentPage < $this->itemsPerPage) {
                $this->collections[] = $item;
                $this->itemsInCurrentPage = count($this->collections);
            }
        }

        return $this->buildInternal();
    }
}
