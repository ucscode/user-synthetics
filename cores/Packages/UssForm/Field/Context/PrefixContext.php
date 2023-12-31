<?php

namespace Ucscode\UssForm\Field\Context;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Foundation\AbstractFieldContext;
use Ucscode\UssForm\Resource\Service\FormUtils;

class PrefixContext extends AbstractFieldContext
{
    protected string $name = 'prefix';

    protected function created(): void
    {
        $this->addClass('input-affix');
        $this->store->prefixAppended = false;
    }

    public function setValue(string|UssElement|null $value): self
    {
        $this->detachElement();
        parent::setValue($value);
        !is_null($value) && $this->isApplicable() ? $this->attachElement() : null;
        $this->regulateComponent();
        return $this;
    }

    protected function attachElement(): void
    {
        $containerContext = $this->elementContext->container;

        $containerContext->removeClass('input-single')->addClass('input-group');

        $valueIsButton = (new FormUtils())->isButton($this->value);

        $classes = ['input-group-text', 'input-affix-custom'];

        array_walk($classes, fn ($value) => $this->removeClass($value));

        $this->addClass(
            is_string($this->value) || !$valueIsButton ? $classes[0] : $classes[1]
        );

        $this->coupleElement($valueIsButton);
        $this->store->{$this->name . 'Appended'} = true;
    }

    protected function detachElement(): void
    {
        $valueIsButton = (new FormUtils())->isButton($this->value);
        $container = $this->elementContext->container->getElement();
        $container->removeChild($valueIsButton ? $this->value : $this->element);
        $this->store->{$this->name . 'Appended'} = false;
    }

    protected function coupleElement(bool $valueIsButton): void
    {
        $this->elementContext
            ->container->getElement()
            ->prependChild(
                $valueIsButton ? $this->value : $this->element
            );
    }

    protected function isApplicable(): bool
    {
        $widgetContext = $this->elementContext->widget;
        return
            !$widgetContext->isCheckable() &&
            !$widgetContext->isHidden() &&
            !$widgetContext->isButton();
    }

    protected function regulateComponent(): void
    {
        if(!$this->store->prefixAppended && !$this->store->suffixAppended) {
            $this->elementContext->container
                ->removeClass('input-group')
                ->addClass('input-single');
        }
    }
}
