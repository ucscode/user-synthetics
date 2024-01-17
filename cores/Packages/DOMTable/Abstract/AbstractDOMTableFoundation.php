<?php

namespace Ucscode\DOMTable\Abstract;

use mysqli_result;
use Ucscode\DOMTable\Interface\DOMTableKernelInterface;
use Ucscode\UssElement\UssElement;

abstract class AbstractDOMTableFoundation implements DOMTableKernelInterface
{
    protected int $totalItems = 0;
    protected int $totalPages = 0;
    protected int $itemsPerPage = 10;
    protected int $currentPage = 1;
    protected int $itemsInCurrentPage = 0;
    protected ?int $prevPage = null;
    protected ?int $nextPage = null;
    protected array $columns = [];
    protected bool $displayFooter = false;
    protected mysqli_result|array $data;
    protected ?UssElement $tableWrapper = null;
    protected ?UssElement $tableContainer = null;
    protected ?UssElement $table = null;
    protected ?UssElement $thead = null;
    protected ?UssElement $tbody = null;
    protected ?UssElement $tfoot = null;
    protected ?UssElement $emptinessElement = null;
}