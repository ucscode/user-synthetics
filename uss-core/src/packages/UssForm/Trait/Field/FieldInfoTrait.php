<?php

namespace Ucscode\UssForm\Trait\Field;

use Ucscode\UssElement\UssElement;

trait FieldInfoTrait
{
    protected array $info = [
        'element' => null,
        'value' => null,
        'hidden' => false
    ];

    public function getInfoElement(): UssElement
    {
        return $this->info['element'];
    }

    public function setInfoAttribute(string $name, ?string $value, bool $append = false): self
    {
        return $this->attributeSetter($this->info['element'], $name, $value, $append);
    }

    public function getInfoAttribute(string $name): ?string
    {
        return $this->info['element']->getAttribute($name);
    }

    public function removeInfoAttribute(string $name, ?string $detach = null): self
    {
        return $this->attributeRemover($this->info['element'], $name, $detach);
    }

    public function setInfoMessage(null|string|UssElement $value, ?string $icon = null): self
    {
        $this->info['value'] = $value;
        $this->insertElementValue(
            $this->info['element'],
            $value,
            func_num_args() === 2 ? $icon : 'bi bi-info-circle'
        );
        return $this;
    }

    public function getInfoMessage(): null|string|UssElement
    {
        return $this->info['value'];
    }

    public function setInfoHidden(bool $status): self
    {
        if($this->isHiddenWidget()) {
            $status = true;
        };
        $this->info['hidden'] = $status;

        if(!$this->info['hidden']) {
            $this->container['element']->insertBefore(
                $this->info['element'],
                $this->widgetContainer['element']
            );
        } else {
            $this->info['element']
                ->getParentElement()
                ->removeChild($this->info['element']);
        }

        return $this;
    }

    public function isInfoHidden(): bool
    {
        return $this->info['hidden'];
    }
}
