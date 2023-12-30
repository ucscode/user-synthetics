<?php

namespace Ucscode\UssForm\Trait\Field;

use Ucscode\UssElement\UssElement;

trait FieldWidgetMinorTrait
{
    public function setWidgetSuffix(null|string|UssElement $appendant): self
    {
        $this->widget['appendant'] = $this->refactorInputGroupContent($appendant);
        $this->extendWidgetAside(function () {
            $this->widgetContainer['element']->insertAfter(
                $this->widget['appendant'],
                $this->widget['element']
            );
        });
        return $this;
    }

    public function getWidgetSuffix(): ?UssElement
    {
        return $this->widget['appendant'];
    }

    public function removeWidgetSuffix(): self
    {
        if($this->widget['appendant']) {
            $this->widget['appendant']
                ->getParentElement()
                ->removeChild($this->widget['appendant']);

            $this->widget['appendant'] = null;
        }
        return $this;
    }

    public function setWidgetPrefix(null|string|UssElement $prependant): self
    {
        $this->widget['prependant'] = $this->refactorInputGroupContent($prependant);
        $this->extendWidgetAside(function () {
            if($this->widget['prependant']) {
                $this->widgetContainer['element']->insertBefore(
                    $this->widget['prependant'],
                    $this->widget['element']
                );
            }
        });
        return $this;
    }

    public function getWidgetPrefix(): ?UssElement
    {
        return $this->widget['prependant'];
    }

    public function removeWidgetPrefix(): self
    {
        if($this->widget['prependant']) {
            $this->widget['prependant']
                ->getParentElement()
                ->removeChild($this->widget['prependant']);

            $this->widget['prependant'] = null;
        }
        return $this;
    }

    /**
     * For: Widget Modifier
     */
    public function setWidgetOptions(array $options): self
    {
        $this->widget['options']['values'] = $options;
        $this->rebuildWidgetOptionsElements($options);
        return $this;
    }

    public function setWidgetOption(string $key, string $displayValue): self
    {
        $optionElement = $this->widget['options']['elements'][$key] ?? null;
        if(!$optionElement) {
            $optionElement = $this->createOptionElement($key, $displayValue);
            $this->widget['element']->appendChild($optionElement);
        } else {
            $optionElement->setContent($displayValue);
        }
        $this->widget['options']['values'][$key] = $displayValue;
        $this->widget['options']['elements'][$key] = $optionElement;
        return $this;
    }

    public function removeWidgetOption(string $key): self
    {
        if($this->hasWidgetOption($key)) {
            $optionElement = $this->getWidgetOptionElement($key);
            unset($this->widget['options']['values'][$key]);
            unset($this->widget['options']['elements'][$key]);
            $optionElement->getParentElement()->removeChild($optionElement);
        }
        return $this;
    }

    public function getWidgetOptions(): array
    {
        return $this->widget['options']['values'];
    }

    public function hasWidgetOption(string $key): bool
    {
        return array_key_exists($key, $this->widget['options']['values']);
    }

    public function getWidgetOptionElement(string $key): ?UssElement
    {
        return $this->widget['options']['elements'][$key] ?? null;
    }
}
