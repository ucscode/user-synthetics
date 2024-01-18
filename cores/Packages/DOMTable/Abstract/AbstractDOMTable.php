<?php

namespace Ucscode\DOMTable\Abstract;

use Exception;
use Generator;
use mysqli_result;
use Ucscode\DOMTable\Interface\DOMTableIteratorInterface;
use Ucscode\UssElement\UssElement;

abstract class AbstractDOMTable extends AbstractDOMTableFactory
{
    public readonly string $tablename;
    protected ?DOMTableIteratorInterface $iteratorInterface;
    protected array $collections;
    protected bool $hasBoundary = false;

    public function __construct(?string $tablename = null)
    {
        $this->tablename = $tablename ?: uniqid('_');
        $this->createTableElements();
        $this->orientTableElements();
    }

    protected function getGenerator(): Generator
    {
        if($this->data instanceof mysqli_result) {
            $this->data->data_seek(0);
            while($item = $this->data->fetch_assoc()) {
                $item = $this->fabricateItem($item, $this->iteratorInterface);
                if($item) {
                    yield $item;
                }
            }
            return;
        } 
        foreach($this->data as $item) {
            $item = $this->fabricateItem($item, $this->iteratorInterface);
            if($item) {
                yield $item;
            }
        }
    }
    
    protected function fabricateItem(array $item, ?DOMTableIteratorInterface $fabricator): ?array
    {
        $extraColumns = array_diff(array_keys($this->columns), array_keys($item));
        !empty($extraColumns) ? $item = array_merge($item, array_fill_keys($extraColumns, null)) : null;
        $fabricator ? $item = $fabricator->forEachItem($item) : null;
        return $item;
    }

    protected function buildInternal(): UssElement
    {
        $this->createPaginationUnits();
        $this->createTableCaptions($this->thead);
        $this->enlistTableContents();
        $this->displayFooter ? $this->createTableCaptions($this->tfoot) : null;
        $this->isolateEmptyContext();
        return $this->tableWrapper;
    }

    protected function createPaginationUnits(): void
    {
        $this->totalPages = ceil($this->totalItems / $this->itemsPerPage);
        $this->nextPage = $this->currentPage + 1;
        $this->prevPage = $this->currentPage - 1;
        $this->nextPage > $this->totalPages ? $this->nextPage = null : null;
        $this->prevPage < 1 ? $this->prevPage = null : null;
    }

    protected function createTableCaptions(UssElement $parentElement): void
    {
        $tr = $this->createElement(UssElement::NODE_TR);
        foreach($this->columns as $caption) {
            $th = new UssElement(UssElement::NODE_TH);
            $caption instanceof UssElement ? $th->appendChild($caption) : $th->setContent($caption);
            $tr->appendChild($th);
        };
        $parentElement->appendChild($tr);
    }

    protected function enlistTableContents(): void
    {
        foreach($this->collections as $item) {
            $tr = $this->createElement(UssElement::NODE_TR);
            foreach(array_keys($this->columns) as $key) {
                $value = $item[$key];
                $td = $this->createElement(UssElement::NODE_TD);
                $value instanceof UssElement ? $td->appendChild($value) : $td->setContent($value);
                $tr->appendChild($td);
            };
            $this->tbody->appendChild($tr);
        };
    }
    
    protected function isolateEmptyContext(): void
    {
        if(!$this->itemsInCurrentPage) {
            $container = 
                $this->createElement(UssElement::NODE_DIV, 'emptiness-container')
                ->appendChild($this->emptinessElement);
            $this->tableWrapper->appendChild($container);
        }
    }

    protected function setBoundary(): void
    {
        if($this->hasBoundary) {
            throw new Exception(
                "Call to build method can only be called once after DOMTable Configuration. Consider creating a new instance"
            );
        }
        $this->hasBoundary = true;
    }
}
