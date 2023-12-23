<?php

namespace Ucscode\UssForm\Trait\Fieldstack;

use Ucscode\UssElement\UssElement;

trait FieldstackTitleTrait
{
    /**
     * @method getTitleElement
     */
    public function getTitleElement(): UssElement
    {
        return $this->title['element'];
    }

    /**
     * @method setTitleAttribute
     */
    public function setTitleAttribute(string $name, string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->title['element'], $name, $value, $append);
    }

    /**
     * @method getTitleAttribute
     */
    public function getTitleAttribute(string $name): ?string
    {
        return $this->title['element']->getAttribute($name);
    }

    /**
     * @method removeTitleAttribute
     */
    public function removeTitleAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->title['element'], $name, $detach);
    }

    /**
     * @method setTitleValue
     */
    public function setTitleValue(?string $value): self
    {
        $this->valueSetter($this->title, $value);
        $this->refactorTitle();
        return $this;
    }

    /**
     * @method getTitleValue
     */
    public function getTitleValue(): ?string
    {
        return $this->title['value'];
    }

    /**
     * @method hideTitle
     */
    public function hideTitle(bool $status): self
    {
        $this->title['hidden'] = $status;
        $this->refactorTitle();
        return $this;
    }

    /**
     * @method isTitleHidden
     */
    public function isTitleHidden(): bool
    {
        return $this->title['hidden'];
    }

    /**
     * @method refactorTitle
     */
    private function refactorTitle(): void
    {
        $this->isTitleHidden() ? 
            $this->hideElement($this->title['element']) : 
            $this->outerContainer['element']->prependChild($this->title['element']);
    }
}
