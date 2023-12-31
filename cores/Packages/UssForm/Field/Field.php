<?php

namespace Ucscode\UssForm\Field;

use Ucscode\UssForm\Field\Manifest\AbstractField;
use Ucscode\UssForm\Field\Foundation\ElementContext;
use Ucscode\UssForm\Gadget\Gadget;
use Ucscode\UssForm\Resource\Facade\Position;
use Ucscode\UssForm\Resource\Service\FieldUtils;

class Field extends AbstractField
{
    protected array $gadgets = [];

    public function getElementContext(): ElementContext
    {
        return $this->elementContext;
    }

    public function addGadget(string $name, Gadget $gadget): self
    {
        $oldGadget = $this->getGadget($name);
        $this->gadgets[$name] = $gadget;
        $this->swapField(
            $gadget->container->getElement(),
            $oldGadget?->container->getElement()
        );
        (new FieldUtils())->welcomeGadget($name, $gadget);
        return $this;
    }

    public function getGadget(string $name): ?Gadget
    {
        return $this->gadgets[$name] ?? null;
    }

    public function removeGadget(string|Gadget $context): ?Gadget
    {
        if($this->hasGadget($context)) {
            $gadget = $context instanceof Gadget ? $context : $this->getGadget($context);
            $name = $this->getGadgetName($gadget);
            unset($this->gadgets[$name]);
            $element = $gadget->container->getElement();
            $element->getParentElement()->removeChild($element);
            return $gadget;
        }
        return null;
    }

    public function hasGadget(string|Gadget $gadget): bool
    {
        if($gadget instanceof Gadget) {
            return in_array($gadget, $this->gadgets, true) || $gadget === $this->elementContext->gadget;
        }
        return !!$this->getGadget($gadget);
    }

    public function getGadgetName(Gadget $gadget): ?string
    {
        $name = array_search($gadget, $this->gadgets, true);
        return $name !== false ? $name : null;
    }

    public function getGadgets(): array
    {
        return $this->gadgets;
    }

    public function setGadgetPosition(string|Gadget $gadget, Position $position, string|Gadget $targetGadget): bool
    {
        $gadget = $gadget instanceof Gadget ? $gadget : $this->getGadget($gadget);
        $targetGadget = $targetGadget instanceof Gadget ? $targetGadget : $this->getGadget($targetGadget);

        if($this->hasGadget($gadget) && $this->hasGadget($targetGadget)) {
            $gadgetElement = $gadget->container->getElement();
            $targetElement = $targetGadget->container->getElement();
            $gadgetWrapper = $this->getElementContext()->gadgetWrapper->getElement();
            
            $position === Position::AFTER ?
                $gadgetWrapper->insertAfter($gadgetElement, $targetElement) :
                $gadgetWrapper->insertBefore($gadgetElement, $targetElement);

            return true;
        }

        return false;
    }
}
