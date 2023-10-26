<?php

namespace Ucscode\DOMTable;

use Exception;
use mysqli_result;
use Ucscode\UssElement\UssElement;

class DOMTable extends AbstractDOMTable
{
    public readonly string $tablename;
    public readonly UssElement $tableCase;
    public readonly UssElement $tableWidget;
    public readonly UssElement $tableContainer;
    public readonly UssElement $tablePaginator;
    public readonly UssElement $table;
    public readonly UssElement $thead;
    public readonly UssElement $tbody;
    public readonly UssElement $tfoot;
    private null|DOMTableInterface $fabricator;
    protected bool $displayFooter = false;

    public function __construct(?string $tablename = null)
    {
        $this->tablename = empty($tablename) ? ('_' . uniqid()) : $tablename;
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
     * @method build
     */
    public function build(null|DOMTableInterface $fabricator = null): self
    {
        $this->countResource(__METHOD__);
        $this->fabricator = $fabricator;
        $startIndex = ($this->page - 1) * $this->chunks;

        if($this->data instanceof mysqli_result) {
            $result = $this->buildFromMysqli($startIndex);
        } else {
            $result = $this->buildFromArray($startIndex);
        };
        
        $this->developeTableNodes();
        $this->createTHead($this->thead);
        $this->createTBody($result);

        if($this->displayFooter) {
            $this->createTHead($this->tfoot);
        }

        $this->restructureTableElements();

        return $this;
    }

    /**
     * @method getHTML
     */
    public function getHTML(): string
    {
        return $this->tableCase->getHTML();
    }

    /**
     * @method countResource
     */
    protected function countResource(string $method)
    {
        if(!isset($this->data)) {
            throw new \Exception(
                sprintf(
                    '%s: Cannot build table context; No data provided',
                    $method
                )
            );
        }
        $this->rows = is_array($this->data) ? count($this->data) : $this->data->num_rows;
        $this->maxPage = ceil($this->rows / $this->chunks);
    }

    /**
     * @method buildMysqliData
     */
    protected function buildFromMysqli(int $startIndex): array
    {
        $result = [];
        $this->data->data_seek($startIndex);
        while($data = $this->data->fetch_assoc()) {
            if(count($result) === $this->chunks) {
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
        $result = array_slice($this->data, $startIndex, $this->chunks);
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
            $th->setContent($display);
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
                $td->setContent($value);
                $tr->appendChild($td);
            };
            $this->tbody->appendChild($tr);
        };
        $this->table->appendChild($this->tbody);
    }

    /**
     * @method restructureTableElements
     */
    protected function restructureTableElements(): void
    {
        $this->tableCase->appendChild($this->tableWidget);
        $this->tableContainer->appendChild($this->table);
        $this->tableCase->appendChild($this->tableContainer);
        $this->tableCase->appendChild($this->tablePaginator);
    }

    /**
     * @method createTableNodes
     */
    protected function developeTableNodes(): void
    {
        $this->tableCase = new UssElement(UssElement::NODE_DIV);
        $this->tableCase->setAttribute('class', 'table-case');
        $this->tableCase->setAttribute('id', 'table-' . $this->tablename);

        $this->tableWidget = new UssElement(UssElement::NODE_DIV);
        $this->tableWidget->setAttribute('class', 'table-widgets');

        $this->tableContainer = new UssElement(UssElement::NODE_DIV);
        $this->tableContainer->setAttribute('class', 'table-responsive');

        $this->table = new UssElement(UssElement::NODE_TABLE);
        $this->table->setAttribute('class', 'table');

        $this->thead = new UssElement(UssElement::NODE_THEAD);
        $this->tbody = new UssElement(UssElement::NODE_TBODY);
        $this->tfoot = new UssElement(UssElement::NODE_TFOOT);

        $this->tablePaginator = new UssElement(UssElement::NODE_DIV);
        $this->tablePaginator->setAttribute('class', 'table-paginator');

        $this->emptyContext = new UssElement(UssElement::NODE_DIV);
        $this->emptyContext->setContent("No Data Found");
    }
}
