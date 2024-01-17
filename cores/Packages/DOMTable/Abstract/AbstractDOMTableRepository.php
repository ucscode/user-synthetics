<?php

namespace Ucscode\DOMTable\Abstract;

use mysqli_result;
use Ucscode\UssElement\UssElement;

abstract class AbstractDOMTableRepository extends AbstractDOMTableFoundation
{
    public function gettotalItems(): int
    {
        return $this->totalItems;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function countItemsInCurrentPage(): int
    {
        return $this->itemsInCurrentPage;
    }

    public function getNextPage(): ?int
    {
        return $this->nextPage;
    }

    public function getPrevPage(): ?int
    {
        return $this->prevPage;
    }

    public function setItemsPerPage(int $chunks): self
    {
        $this->itemsPerPage = $chunks;
        return $this;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function setCurrentPage(int $page): self
    {
        $this->currentPage = abs($page);
        return $this;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function setColumns(array $columns): self
    {
        $keys = [];
        foreach($columns as $key => $value) {
            if(is_numeric($key)) {
                $key = $value;
            };
            $keys[] = $key;
        };
        $columns = array_combine($keys, $columns);
        $this->columns = $columns;
        return $this;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function setColumn(string $key, ?string $displayText = null): self
    {
        if(is_null($displayText)) {
            $displayText = $key;
        };
        $this->columns[$key] = $displayText;
        return $this;
    }

    public function removeColumn(string $key): self
    {
        if(array_key_exists($key, $this->columns)) {
            unset($this->columns[$key]);
        }
        return $this;
    }

    public function getData(): mysqli_result|array
    {
        return $this->data;
    }

    public function setEmptinessElement(?UssElement $context): self
    {
        $this->emptinessElement = $context;
        return $this;
    }

    public function getEmptinessElement(): UssElement
    {
        return $this->emptinessElement;
    }

    public function setDisplayFooter(bool $status): self
    {
        $this->displayFooter = $status;
        return $this;
    }

    public function getDisplayFooter(): bool
    {
        return $this->displayFooter;
    }

    public function getTableWrapperElement(): ?UssElement
    {
        return $this->tableWrapper;
    }

    public function getTableContainerElement(): ?UssElement
    {
        return $this->tableContainer;
    }

    public function getTableElement(): ?UssElement
    {
        return $this->table;
    }

    public function getTheadElement(): ?UssElement
    {
        return $this->thead;
    }

    public function getTbodyElement(): ?UssElement
    {
        return $this->tbody;
    }

    public function getTfootElement(): ?UssElement
    {
        return $this->tfoot;
    }
}