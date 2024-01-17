<?php

namespace Ucscode\DOMTable\Interface;

use mysqli_result;
use Ucscode\UssElement\UssElement;

interface DOMTableRepositoryInterface
{
    public function gettotalItems(): int;
    public function getTotalPages(): int;
    public function countItemsInCurrentPage(): int;
    public function getNextPage(): ?int;
    public function getPrevPage(): ?int;
    public function setItemsPerPage(int $chunks): self;
    public function getItemsPerPage(): int;
    public function setCurrentPage(int $page): self;
    public function getCurrentPage(): int;
    public function setColumns(array $columns): self;
    public function getColumns(): array;
    public function setColumn(string $key, ?string $displayText = null): self;
    public function removeColumn(string $key): self;
    public function getData(): mysqli_result|array;
    public function setEmptinessElement(?UssElement $context): self;
    public function getEmptinessElement(): UssElement;
    public function setDisplayFooter(bool $status): self;
    public function getDisplayFooter(): bool;
    public function getTableWrapperElement(): ?UssElement;
    public function getTableContainerElement(): ?UssElement;
    public function getTableElement(): ?UssElement;
    public function getTheadElement(): ?UssElement;
    public function getTbodyElement(): ?UssElement;
    public function getTfootElement(): ?UssElement;
}