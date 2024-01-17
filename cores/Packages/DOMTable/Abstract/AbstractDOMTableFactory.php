<?php

namespace Ucscode\DOMTable\Abstract;

use Ucscode\UssElement\UssElement;

abstract class AbstractDOMTableFactory extends AbstractDOMTableRepository
{
    protected function createTableElements(): void
    {
        $this->tableWrapper = $this->createElement(UssElement::NODE_DIV, 'table-wrapper');
        $this->tableContainer = $this->createElement(UssElement::NODE_DIV, 'table-responsive table-container');
        $this->table = $this->createElement(UssElement::NODE_TABLE, 'table');
        $this->thead = $this->createElement(UssElement::NODE_THEAD);
        $this->tbody = $this->createElement(UssElement::NODE_TBODY);
        $this->tfoot = $this->createElement(UssElement::NODE_TFOOT);
        $this->emptinessElement = $this->createElement(UssElement::NODE_DIV, 'border p-4 text-center', 'No Item Found');
    }

    protected function orientTableElements(): void
    {
        $this->tableWrapper->appendChild($this->tableContainer);
        $this->tableContainer->appendChild($this->table);
        $this->table->appendChild($this->thead);
        $this->table->appendChild($this->tbody);
        $this->table->appendChild($this->tfoot);
    }

    protected function createElement(string $nodeName, ?string $className = null, ?string $content = null): UssElement
    {
        $element = new UssElement($nodeName);
        $className ? $element->setAttribute('class', $className) : null;
        $content ? $element->setContent($content) : null;
        return $element;
    }
}