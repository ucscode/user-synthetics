<?php

namespace Ucscode\UssForm\Form\Manifest;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Collection\Collection;
use Ucscode\UssForm\Form\Attribute;
use Ucscode\UssForm\Resource\Facade\PositionInterface;

interface FormInterface extends PositionInterface
{
    public function addCollection(string $name, Collection $collection): self;
    public function getCollection(string $name): ?Collection;
    public function removeCollection(string|Collection $context): ?Collection;
    public function hasCollection(string|Collection $context): bool;
    public function getCollectionName(Collection $collection): ?string;
    public function getCollections(): array;
    public function getElement(): UssElement;
    public function getAttribute(): Attribute;
    public function export(): string;
    public function setCollectionPosition(string|Collection $collection, int $position, string|Collection $targetCollection): bool;
}
