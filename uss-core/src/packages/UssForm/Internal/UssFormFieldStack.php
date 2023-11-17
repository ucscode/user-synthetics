<?php

namespace Ucscode\UssForm\Internal;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Abstraction\AbstractUssFormFieldStack;
use Ucscode\UssForm\UssFormField;

class UssFormFieldStack extends AbstractUssFormFieldStack
{
    /**
     * @method addField
     */
    public function addField(string $name, UssFormField $field): self
    {
        $previousField = $this->getField($name);
        if(!$previousField) {
            $this->innerContainer['element']->appendChild(
                $field->getFieldAsElement()
            );
        } else {
            if($previousField !== $field) {
                $this->innerContainer['element']->replaceChild(
                    $field->getFieldAsElement(),
                    $previousField->getFieldAsElement()
                );
            }
        }
        if($field->hasLineBreak()) {
            $this->innerContainer['element']->insertAfter(
                $field->getLineBreak(),
                $field->getFieldAsElement()
            );
        };
        $this->fields[$name] = $field;
        return $this;
    }

    /**
     * @method addElement
     */
    public function addElement(string $name, UssElement $element): self
    {
        $this->elements[$name] = $element;
        $this->innerContainer['element']->appendChild($element);
        return $this;
    }

    /**
     * @method getFields
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @method getElements
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @method getField
     */
    public function getField(string $name): ?UssFormField
    {
        return $this->fields[$name] ?? null;
    }

    /**
     * @method getElement
     */
    public function getElement(string $name): ?UssElement
    {
        return $this->elements[$name] ?? null;
    }

    /**
     * @method removeField
     */
    public function removeField(string $name): self
    {
        if(array_key_exists($name, $this->fields)) {
            $field = $this->getField($name);
            unset($this->fields[$name]);
            if($field) {
                $element = $field->getFieldAsElement();
                $lineBreak = $field->getLineBreak();
                if($element->getParentElement() === $this->innerContainer['element']) {
                    $this->innerContainer['element']->removeChild($element);
                    if($lineBreak) {
                        $this->innerContainer['element']->removeChild($lineBreak);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * @method removeElement
     */
    public function removeElement(string $name): self
    {
        if(array_key_exists($name, $this->elements)) {
            $element = $this->getElement($name);
            unset($this->elements[$name]);
            if($element && $element->getParentElement() === $this->innerContainer['element']) {
                $this->innerContainer['element']->removeChild($element);
            }
        }
        return $this;
    }

    /**
     * @method getOuterContainerElement
     */
    public function getOuterContainerElement(): UssElement
    {
        return $this->outerContainer['element'];
    }

    /**
     * @method setOuterContainerAttribute
     */
    public function setOuterContainerAttribute(string $name, string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->outerContainer['element'], $name, $value, $append);
    }

    /**
     * @method getOuterContainerAttribute
     */
    public function getOuterContainerAttribute(string $name): ?string
    {
        return $this->outerContainer['element']->getAttribute($name);
    }

    /**
     * @method removeOuterContainerAttribute
     */
    public function removeOuterContainerAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->outerContainer['element'], $name, $detach);
    }

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
        return $this->valueSetter($this->title, $value);
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
        return $this;
    }

    /**
     * @method isTitleHidden
     */
    public function isHiddenTitle(): bool
    {
        return $this->title['hidden'];
    }

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
        return $this;
    }

    /**
     * @method isSubtitleHidden
     */
    public function isHiddenSubtitle(): bool
    {
        return $this->subtitle['hidden'];
    }

    /**
     * @method getInstructionElement
     */
    public function getInstructionElement(): UssElement
    {
        return $this->instruction['element'];
    }

    /**
     * @method setInstructionAttribute
     */
    public function setInstructionAttribute(string $name, string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->instruction['element'], $name, $value, $append);
    }

    /**
     * @method getInstructionAttribute
     */
    public function getInstructionAttribute(string $name): ?string
    {
        return $this->instruction['element']->getAttribute($name);
    }

    /**
     * @method removeInstructionAttribute
     */
    public function removeInstructionAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->instruction['element'], $name, $detach);
    }

    /**
     * @method setInstructionValue
     */
    public function setInstructionValue(null|string|UssElement $value): self
    {
        return $this->valueSetter($this->instruction, $value);
    }

    /**
     * @method getInstructionValue
     */
    public function getInstructionValue(): UssElement|string|null
    {
        return $this->instruction['value'];
    }

    /**
     * @method hideInstruction
     */
    public function hideInstruction(bool $status): self
    {
        $this->instruction['hidden'] = $status;
        return $this;
    }

    /**
     * @method isInstructionHidden
     */
    public function isHiddenInstruction(): bool
    {
        return $this->instruction['hidden'];
    }

    /**
     * @method getInnerContainerElement
     */
    public function getInnerContainerElement(): UssElement
    {
        return $this->innerContainer['element'];
    }

    /**
     * @method setInnerContainerAttribute
     */
    public function setInnerContainerAttribute(string $name, string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->innerContainer['element'], $name, $value, $append);
    }

    /**
     * @method getInnerContainerAttribute
     */
    public function getInnerContainerAttribute(string $name): ?string
    {
        return $this->innerContainer['element']->getAttribute($name);
    }

    /**
     * @method removeInnerContainerAttribute
     */
    public function removeInnerContainerAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->innerContainer['element'], $name, $detach);
    }

    /**
     * @method disableOuterContainer
     */
    public function setFieldStackDisabled(bool $status): self
    {
        if($status) {
            $this->outerContainer['element']->setAttribute('disabled', 'disabled');
        } else {
            $this->outerContainer['element']->removeAttribute('disabled');
        }
        return $this;
    }

    /**
     * @method isFieldStackDisabled
     */
    public function isFieldStackDisabled(): bool
    {
        return $this->outerContainer['element']->hasAttribute('disabled');
    }

    /**
     * @method getFieldStackAsElement
     */
    public function getFieldStackAsElement(): UssElement
    {
        if($this->title['value'] && !$this->title['hidden']) {
            $this->outerContainer['element']->prependChild($this->title['element']);
        }

        $this->outerContainer['element']->appendChild($this->innerContainer['element']);

        if($this->subtitle['value'] && !$this->subtitle['hidden']) {
            $this->outerContainer['element']->insertBefore(
                $this->subtitle['element'],
                $this->innerContainer['element']
            );
        }

        if($this->instruction['value'] && !$this->instruction['hidden']) {
            $this->outerContainer['element']->insertBefore(
                $this->instruction['element'],
                $this->innerContainer['element']
            );
        }

        return $this->outerContainer['element'];
    }

    /**
     * @method getFieldStackAsHTML
     */
    public function getFieldStackAsHTML(): string
    {
        return $this->getFieldStackAsElement()->getHTML(true);
    }
}
