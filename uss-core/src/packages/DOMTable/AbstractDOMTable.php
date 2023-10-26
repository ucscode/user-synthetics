<?php

namespace Ucscode\DOMTable;

use mysqli_result;
use Ucscode\UssElement\UssElement;

abstract class AbstractDOMTable 
{
    protected int $chunks = 10;
    protected int $page = 1;
    protected array $columns = [];
    protected array|mysqli_result $data;
    protected UssElement $emptyContext;
    protected int $rows;
    protected int $maxPage;

    /**
     * @method getRows
     */
    public function getRows(): int
    {
        return $this->rows;
    }

    /**
     * @method getPages
     */
    public function getMaxPages(): int
    {
        return $this->maxPage;
    }

    /**
     * @method setChunks
     */
    public function setChunks(int $chunks): self
    {
        $this->chunks = $chunks;
        return $this;
    }

    /**
     * @method getChunks
     */
    public function getChunks(): int
    {
        return $this->chunks;
    }

    /**
     * @method setPage
     */
    public function setPage(int $page): self
    {
        $this->page = abs($page);
        return $this;
    }

    /**
     * @method getPage
     */
    public function getPage(): int
    {
        return $this->page;
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
    public function setEmptyContext(UssElement $context): self
    {
        $this->emptyContext = $context;
        return $this;
    }

    /**
     * @method getEmptyContext
     */
    public function getEmptyContext(): UssElement
    {
        return $this->emptyContext;
    }
}