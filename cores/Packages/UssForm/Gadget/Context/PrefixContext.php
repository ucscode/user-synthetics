<?php

namespace Ucscode\UssForm\Gadget\Context;

use Ucscode\UssForm\Gadget\Foundation\AbstractGadgetContext;
use Ucscode\UssForm\Resource\Service\FormUtils;
use Ucscode\UssElement\UssElement;

class PrefixContext extends AbstractGadgetContext
{
    protected string $name = 'prefixed';

    protected function created(): void
    {
        $this->addClass('input-affix');
        $this->store->{$this->name} = false;
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
        $containerContext = $this->gadget->container;
        $containerContext->removeClass('input-single')->addClass('input-group');
        $valueIsButton = (new FormUtils())->isButton($this->value);
        $classes = ['input-group-text', 'input-affix-custom'];

        array_walk($classes, fn ($value) => $this->removeClass($value));
        
        $this->addClass(is_string($this->value) || !$valueIsButton ? $classes[0] : $classes[1]);
        $this->coupleElement($valueIsButton);
        $this->store->{$this->name} = true;
    }

    protected function detachElement(): void
    {
        $valueIsButton = (new FormUtils())->isButton($this->value);
        $container = $this->gadget->container->getElement();
        $container->removeChild($valueIsButton ? $this->value : $this->element);
        $this->store->{$this->name} = false;
    }

    protected function coupleElement(bool $valueIsButton): void
    {
        $container = $this->gadget->container->getElement();
        $container->prependChild($valueIsButton ? $this->value : $this->element);
    }

    protected function isApplicable(): bool
    {
        $widgetContext = $this->gadget->widget;
        return
            !$widgetContext->isCheckable() &&
            !$widgetContext->isHidden() &&
            !$widgetContext->isButton();
    }

    protected function regulateComponent(): void
    {
        if(!$this->store->prefixed && !$this->store->suffixed) {
            $this->gadget->container
                ->removeClass('input-group')
                ->addClass('input-single');
        }
    }
}