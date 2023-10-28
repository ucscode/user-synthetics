<?php

namespace Ucscode\DOMTable;

use Exception;
use Generator;
use mysqli_result;
use Ucscode\UssElement\UssElement;

class DOMTable extends AbstractDOMTable
{
    public readonly string $tablename;
    protected ?DOMTableInterface $fabricator;

    public function __construct(?string $tablename = null)
    {
        if(empty($tablename)) {
            $tablename = uniqid('_');
        };
        $this->tablename = $tablename;
        $this->developeTableNodes();
    }

    /**
     * @method setData
     */
    public function setData(array|mysqli_result $iterable, ?DOMTableInterface $fabricator = null): self
    {
        $this->data = $iterable;
        $this->fabricator = $fabricator;
        $this->configureProperty();
        return $this;
    }

    /**
     * @method build
     */
    public function build(): UssElement
    {
        $startIndex = ($this->currentPage - 1) * $this->itemsPerPage;
        $result = [];

        foreach($this->getGenerator() as $key => $item) {
            if(($key < $startIndex) === false) {
                if(count($result) > $this->itemsPerPage - 1) {
                    break;
                }
                $result[] = $item;
            }
        }

        $this->itemsInCurrentPage = count($result);

        $this->createTHead($this->thead);
        $this->createTBody($result);

        if($this->displayFooter) {
            $this->createTHead($this->tfoot);
        }

        $this->tableContainer->appendChild($this->table);

        return $this->tableContainer;
    }

    /**
     * @method generator
     */
    public function getGenerator(): Generator
    {
        if($this->data instanceof mysqli_result) {
            $this->data->data_seek(0);
            while($item = $this->data->fetch_assoc()) {
                $item = $this->fabricateItem($item, $this->fabricator);
                if($item) {
                    yield $item;
                }
            }
        } else {
            foreach($this->data as $key => $item) {
                $item = $this->fabricateItem($item, $this->fabricator);
                if($item) {
                    yield $item;
                }
            }
        };
    }
    /**
     * @method countResource
     */
    protected function configureProperty()
    {
        $this->totalItems = iterator_count($this->getGenerator());
        $this->totalPages = ceil($this->totalItems / $this->itemsPerPage);

        $this->nextPage = $this->currentPage + 1;
        $this->prevPage = $this->currentPage - 1;

        if($this->nextPage > $this->totalPages) {
            $this->nextPage = null;
        };

        if($this->prevPage < 1) {
            $this->prevPage = null;
        }
    }

    /**
     * @method fabricateItem
     */
    protected function fabricateItem(array $item, ?DOMTableInterface $fabricator): ?array
    {
        $extraColunms = array_diff(array_keys($this->columns), array_keys($item));
        if(!empty($extraColunms)) {
            // update extra columns with null values
            foreach($extraColunms as $key) {
                $item[$key] = null;
            }
        };
        if($fabricator) {
            $item = $fabricator->forEachItem($item);
        }
        return $item;
    }

    /**
     * @method createThead
     */
    protected function createThead(UssElement $parentElement): void
    {
        $tr = new UssElement(UssElement::NODE_TR);
        foreach($this->columns as $display) {
            $th = new UssElement(UssElement::NODE_TH);
            if($display instanceof UssElement) {
                $th->appendChild($display);
            } else {
                $th->setContent($display);
            }
            $tr->appendChild($th);
        };
        $parentElement->appendChild($tr);
        $this->table->appendChild($parentElement);
    }

    /**
     * @method createTBody
     */
    protected function createTBody(array $result): void
    {
        foreach($result as $data) {
            $tr = new UssElement(UssElement::NODE_TR);
            foreach(array_keys($this->columns) as $key) {
                $value = $data[$key];
                $td = new UssElement(UssElement::NODE_TD);
                if($value instanceof UssElement) {
                    $td->appendChild($value);
                } else {
                    $td->setContent($value);
                }
                $tr->appendChild($td);
            };
            $this->tbody->appendChild($tr);
        };
        $this->table->appendChild($this->tbody);
    }

    /**
     * @method createTableNodes
     */
    protected function developeTableNodes(): void
    {
        $this->tableContainer = new UssElement(UssElement::NODE_DIV);
        $this->tableContainer->setAttribute('class', 'table-responsive');

        $this->table = new UssElement(UssElement::NODE_TABLE);
        $this->table->setAttribute('class', 'table');

        $this->thead = new UssElement(UssElement::NODE_THEAD);
        $this->tbody = new UssElement(UssElement::NODE_TBODY);
        $this->tfoot = new UssElement(UssElement::NODE_TFOOT);

        if(empty($this->emptinessElement)) {
            $this->emptinessElement = new UssElement(UssElement::NODE_DIV);
            $this->emptinessElement->setContent("No Data Found");
        }
    }
}
