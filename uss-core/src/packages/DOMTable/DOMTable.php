<?php

namespace Ucscode\DOMTable;

use Exception;
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
     * @method build
     */
    public function build(?DOMTableInterface $fabricator = null): string
    {
        $this->countResource(__METHOD__);

        $this->fabricator = $fabricator;
        $startIndex = ($this->currentPage - 1) * $this->rowsPerPage;

        if($this->data instanceof mysqli_result) {
            $result = $this->buildFromMysqli($startIndex);
        } else {
            $result = $this->buildFromArray($startIndex);
        };

        $this->availableRowsInPage = count($result);

        $this->createTHead($this->thead);
        $this->createTBody($result);

        if($this->displayFooter) {
            $this->createTHead($this->tfoot);
        }

        $this->tableContainer->appendChild($this->table);

        return $this->tableContainer->getHTML(true);
    }

    /**
     * @method countResource
     */
    protected function countResource(string $method)
    {
        if(!isset($this->data)) {
            throw new Exception(
                sprintf(
                    '%s: Cannot build table context; No data provided',
                    $method
                )
            );
        }

        $this->totalRows = is_array($this->data) ? count($this->data) : $this->data->num_rows;
        $this->totalPages = ceil($this->totalRows / $this->rowsPerPage);
        
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
     * @method buildMysqliData
     */
    protected function buildFromMysqli(int $startIndex): array
    {
        $result = [];
        $this->data->data_seek($startIndex);
        while($data = $this->data->fetch_assoc()) {
            if(count($result) === $this->rowsPerPage) {
                break;
            }
            $result[] = $this->fabricateData($data);
        };
        return $result;
    }

    /**
     * @method buildArrayData
     */
    protected function buildFromArray(int $startIndex): array
    {
        $result = array_slice($this->data, $startIndex, $this->rowsPerPage);
        foreach($result as $key => $data) {
            $result[$key] = $this->fabricateData($data);
        };
        return $result;
    }

    /**
     * @method fabricateData
     */
    protected function fabricateData(array $data): array
    {
        $extraColunms = array_diff(array_keys($this->columns), array_keys($data));
        if(!empty($extraColunms)) {
            // update extra columns with null values
            foreach($extraColunms as $key) {
                $data[$key] = null;
            }
        };
        if($this->fabricator) {
            $data = $this->fabricator->forEachItem($data);
        }
        return $data;
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
