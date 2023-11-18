<?php

namespace Ucscode\UssForm\Trait\Fieldstack;

use Ucscode\UssElement\UssElement;

trait FieldstackSubtitleTrait
{
    /**
    * @method getSubtitleElement
    */
    public function getSubtitleElement(): UssElement
    {
        return $this->subtitle['element'];
    }

    /**
     * @method setSubtitleAttribute
     */
    public function setSubtitleAttribute(string $name, string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->subtitle['element'], $name, $value, $append);
    }

    /**
     * @method getSubtitleAttribute
     */
    public function getSubtitleAttribute(string $name): ?string
    {
        return $this->subtitle['element']->getAttribute($name);
    }

    /**
     * @method removeSubtitleAttribute
     */
    public function removeSubtitleAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->subtitle['element'], $name, $detach);
    }

    /**
     * @method setSubtitleValue
     */
    public function setSubtitleValue(?string $value): self
    {
        return $this->valueSetter($this->subtitle, $value);
    }

    /**
     * @method getTitleValue
     */
    public function getSubtitleValue(): ?string
    {
        return $this->subtitle['value'];
    }

    /**
     * @method hideSubtitle
     */
    public function hideSubtitle(bool $status): self
    {
        $this->subtitle['hidden'] = $status;
        if($this->subtitle['hidden']) {
            $this->hideElement($this->subtitle['element']);
        } else {
            // subtitle has to be between title and instruction; above inner-container
            if($titleParent = $this->title['element']->getParentElement()) {
                $titleParent->insertAfter(
                    $this->subtitle['element'],
                    $this->title['element']
                );
            } else if($instructionParent = $this->instruction['element']->getParentElement()) {
                $instructionParent->insertBefore(
                    $this->subtitle['element'],
                    $this->instruction['element']
                );
            } else {
                $this->outerContainer['element']->insertBefore(
                    $this->subtitle['element'],
                    $this->innerContainer['element']
                );
            }
        }
        return $this;
    }

    /**
     * @method isSubtitleHidden
     */
    public function isSubtitleHidden(): bool
    {
        return $this->subtitle['hidden'];
    }
}
