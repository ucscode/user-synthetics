<?php

namespace Ucscode\UssForm\Resource\Context;

use Ucscode\UssForm\Resource\Service\FormUtils;
use Ucscode\UssForm\Field\Field;
use Ucscode\UssElement\UssElement;

abstract class AbstractWidgetContext extends AbstractContext
{
    public readonly string $nodeName;
    public readonly ?string $nodeType;

    public function __construct(string $nodeName = Field::NODE_INPUT, ?string $nodeType = Field::TYPE_TEXT)
    {
        [$this->nodeName, $this->nodeType] = (new FormUtils())->regulateElementPrototype($nodeName, $nodeType);
        parent::__construct($this->nodeName);
    }

    protected function created(): void
    {
        switch($this->nodeName) {
            case Field::NODE_SELECT:
                $elementClass = 'form-select';
                break;
            default:
                $elementClass = 'form-control';
                if($this->nodeType) {
                    $this->element->setAttribute('type', $this->getNodeType());
                    $elementClass = $this->isCheckable() ?
                        'form-check-input' :
                        ($this->isButton() ? 'btn btn-primary' : $elementClass);
                }
        }
        $this->element->setAttribute('class', $elementClass ?? '');
    }

    public function setDOMHidden(bool $value): self
    {
        return $this;
    }

    public function setValue(string|UssElement|null $value): self
    {
        $this->value = $value;
        switch($this->nodeName) {
            case Field::NODE_TEXTAREA:
                parent::setValue($value);
                break;
            case Field::NODE_SELECT:
                $value = $this->scalarize($value);
                $this->deselectOption();
                $this->getOptionElement($value)?->setAttribute('selected');
                break;
            default:
                $value = $this->scalarize($value);
                $this->element->setAttribute('value', $value);
        }
        return $this;
    }

    public function setButtonContent(string $content): self
    {
        if($this->nodeName === Field::NODE_BUTTON) {
            $this->element->setContent($content);
        }
        return $this;
    }

    public function isCheckable(): bool
    {
        return (new FormUtils())->isCheckable($this->element);
    }

    public function isButton(): bool
    {
        return (new FormUtils())->isButton($this->element);
    }

    public function isSelective(): bool
    {
        return $this->nodeName === Field::NODE_SELECT;
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
        if($this->nodeName === Field::NODE_INPUT) {
            $hidden ?
                $this->element->setAttribute('type', 'hidden') :
                $this->element->setAttribute('type', $this->getNodeType());
            //$this->elementContext->visualizeContextElements();
        }
        return $this;
    }

    public function isHidden(): bool
    {
        return
            $this->nodeName === Field::NODE_INPUT &&
            $this->element->getAttribute('type') === Field::TYPE_HIDDEN;
    }

    public function setOptions(array $options): self
    {
        if($this->isSelective()) {
            array_walk($options, function ($value, $key) {
                $value = $this->scalarize($value);
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

    protected function getNodeType(): string
    {
        return
            $this->nodeType === Field::TYPE_SWITCH ?
            Field::TYPE_CHECKBOX :
            $this->nodeType;
    }

    protected function scalarize(mixed $value): string
    {
        return is_scalar($value) ?
            (is_bool($value) ? (int)$value : $value) :
            "[" . ucfirst(gettype($value)) . "]";
    }

    protected function deselectOption(): void
    {
        if($this->isSelective()) {
            $option = $this->element->find("[selected]");
            array_walk($option, fn ($option) => $option->removeAttribute('selected'));
        }
    }
}
