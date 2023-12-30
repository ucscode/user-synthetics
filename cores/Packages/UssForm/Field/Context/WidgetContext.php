<?php

namespace Ucscode\UssForm\Field\Context;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Field\Manifest\AbstractFieldContext;

class WidgetContext extends AbstractFieldContext
{
    protected function created(): void
    {
        if($nodeType = $this->elementContext->getField()->nodeType) {
            $this->element->setAttribute(
                'type',
                $nodeType == Field::TYPE_SWITCH ? Field::TYPE_CHECKBOX : $nodeType
            );
        }
    }
    
    public function isCheckable(): bool
    {
        return
            $this->element->nodeName === Field::NODE_INPUT &&
            in_array(
                $this->element->getAttribute('type'),
                [
                    Field::TYPE_CHECKBOX,
                    Field::TYPE_RADIO
                ]
            );
        return false;
    }

    public function isButton(): bool
    {
        return
            $this->element->nodeName === Field::NODE_BUTTON ||
            (
                $this->element->nodeName === Field::NODE_INPUT &&
                in_array(
                    $this->element->getAttribute('type'),
                    [
                        Field::TYPE_BUTTON,
                        Field::TYPE_SUBMIT
                    ]
                )
            );
    }

    public function isSelective(): bool
    {
        return $this->element->nodeName === Field::NODE_SELECT;
    }

    public function setChecked(bool $checked = true): self
    {
        if($this->isCheckable()) {
            $checked ?
                $this->element->setAttribute('checked') :
                $this->element->removeAttribute('checked');
        }
        return $this;
    }

    public function isChecked(): ?bool
    {
        return $this->element->hasAttribute('checked');
    }

    public function setDisabled(bool $status = true): self
    {
        $status ?
            $this->element->setAttribute('disabled') :
            $this->removeAttribute('disabled');
        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->element->hasAttribute('disabled');
    }

    public function setReadonly(bool $status = true): self
    {
        $status ?
            $this->element->setAttribute('readonly') :
            $this->element->removeAttribute('readonly');
        return $this;
    }

    public function isReadonly(): bool
    {
        return $this->element->hasAttribute('readonly');
    }

    public function setRequired(bool $status = true): self
    {
        $status ?
            $this->element->setAttribute('required') :
            $this->element->removeAttribute('required');
        return $this;
    }

    public function isRequired(): bool
    {
        return $this->element->hasAttribute('required');
    }

    public function setHidden(bool $hidden = true): self
    {
        if($this->element->nodeName === Field::NODE_INPUT) {
            $hidden ?
                $this->element->setAttribute('type', 'hidden') :
                $this->element->setAttribute(
                    'type', 
                    $this->elementContext->getField()->nodeType
                );
        }
        return $this;
    }

    public function isHidden(): bool
    {
        return
            $this->element->nodeName === Field::NODE_INPUT &&
            $this->element->getAttribute('type') === Field::TYPE_HIDDEN;
    }

    public function setOptions(array $options): self
    {
        if($this->isSelective()) {
            array_walk($options, function ($value, $key) {
                $value = is_scalar($value) ? (is_bool($value) ? (int)$value : $value) : "[" . ucfirst(gettype($value)) . "]";
                $this->setOption($key, $value);
            });
        }
        return $this;
    }

    public function setOption(string $key, ?string $value): self
    {
        if($this->isSelective()) {
            $option = $this->getOptionElement($key) ?? new UssElement(UssElement::NODE_OPTION);
            $option->setAttribute('value', $key);
            $option->setContent($value);
            $this->hasOption($key) ?: $this->element->appendChild($option);
        }
        return $this;
    }

    public function removeOption(string $key): self
    {
        if($this->isSelective()) {
            if($option = $this->getOptionElement($key)) {
                $option->getParentElement()->removeChild($option);
            }
        }
        return $this;
    }

    public function getOptions(): array
    {
        $options = [];
        if($this->isSelective()) {
            foreach($this->element->getChildren() as $option) {
                $key = $option->getAttribute("value");
                $options[$key] = $option->getContent();
            }
        }
        return $options;
    }

    public function hasOption(string $key): bool
    {
        return !!$this->getOptionElement($key);
    }

    public function getOptionElement(string $key): ?UssElement
    {
        foreach($this->element->getChildren() as $option) {
            if($option->getAttribute("value") == $key) {
                return $option;
            };
        }
        return null;
    }

    public function sortOptions(callable|bool $callback = true): void
    {
        if(is_bool($callback)) {
            $asc = $callback;
            $callback = function (UssElement $a, UssElement $b) use ($asc) {
                return $asc ?
                    strcmp($a->getContent(), $b->getContent()) :
                    strcmp($b->getContent(), $a->getContent());
            };
        }
        $this->element->sortChildren($callback);
    }
}
