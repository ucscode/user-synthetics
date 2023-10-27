<?php

namespace Ucscode\DOMTable;

use mysqli_result;
use Ucscode\UssElement\UssElement;

abstract class AbstractDOMTable
{
    protected int $totalRows = 0;
    protected int $totalPages = 0;
    protected int $rowsPerPage = 10;
    protected int $currentPage = 1;
    protected int $availableRowsInPage = 0;
    protected ?int $prevPage = null;
    protected ?int $nextPage = null;
    protected array $columns = [];
    protected bool $displayFooter = false;
    protected array|mysqli_result $data;

    protected ?UssElement $tableContainer = null;
    protected ?UssElement $table = null;
    protected ?UssElement $thead = null;
    protected ?UssElement $tbody = null;
    protected ?UssElement $tfoot = null;
    protected ?UssElement $emptinessElement = null;

    /**
     * @method getRows
     */
    public function getTotalRows(): int
    {
        return $this->totalRows;
    }

    /**
     * @method getPages
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * @method
     */
    public function getAvailableRowsInPage(): int
    {
        return $this->availableRowsInPage;
    }

    /**
     * @method getNextPage
     */
    public function getNextPage(): ?int
    {
        return $this->nextPage;
    }

    /**
     * @method getPrevPage
     */
    public function getPrevPage(): ?int
    {
        return $this->prevPage;
    }

    /**
     * @method setChunks
     */
    public function setRowsPerPage(int $chunks): self
    {
        $this->rowsPerPage = $chunks;
        return $this;
    }

    /**
     * @method getChunks
     */
    public function getRowsPerPage(): int
    {
        return $this->rowsPerPage;
    }

    /**
     * @method setPage
     */
    public function setCurrentPage(int $page): self
    {
        $this->currentPage = abs($page);
        return $this;
    }

    /**
     * @method getPage
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @method setColumns
     */
    public function setMultipleColumns(array $columns): self
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

    /**
     * @method getColumns
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @method addColumn
     */
    public function setColumn(string $key, ?string $displayText = null): self
    {
        if(is_null($displayText)) {
            $displayText = $key;
        };
        $this->columns[$key] = $displayText;
        return $this;
    }

    /**
     * @method removeColumn
     */
    public function removeColumn(string $key): self
    {
        if(array_key_exists($key, $this->columns)) {
            unset($this->columns[$key]);
        }
        return $this;
    }

    /**
     * @method setData
     */
    public function setData(array|mysqli_result $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @method getData
     */
    public function getData(): array|mysqli_result
    {
        return $this->data;
    }

    /**
     * @method setEmptyContext
     */
    public function setEmptinessElement(UssElement $context): self
    {
        $this->emptinessElement = $context;
        return $this;
    }

    /**
     * @method getEmptyContext
     */
    public function getEmptinessElement(): UssElement
    {
        return $this->emptinessElement;
    }

    /**
     * @method enableTFoot
     */
    public function setDisplayFooter(bool $status): self
    {
        $this->displayFooter = $status;
        return $this;
    }

    /**
     * @method getDisplayFooter
     */
    public function getDisplayFooter(): bool
    {
        return $this->displayFooter;
    }

    /**
     * @method getTableElement
     */
    public function getTableContainerElement(): ?UssElement
    {
        return $this->tableContainer;
    }

    /**
     * @method getTableElement
     */
    public function getTableElement(): ?UssElement
    {
        return $this->table;
    }

    /**
     * @method getTheadElement
     */
    public function getTheadElement(): ?UssElement
    {
        return $this->thead;
    }

    /**
     * @method getTbodyElement
     */
    public function getTbodyElement(): ?UssElement
    {
        return $this->tbody;
    }

    /**
     * @method getTfootElement
     */
    public function getTfootElement(): ?UssElement
    {
        return $this->tfoot;
    }
}
