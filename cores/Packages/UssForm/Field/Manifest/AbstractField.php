<?php

namespace Ucscode\UssForm\Field\Manifest;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Field\Foundation\ElementContext;
use Ucscode\UssForm\Resource\Service\FormUtils;

abstract class AbstractField implements FieldInterface
{
    public readonly string $nodeName;
    public readonly ?string $nodeType;
    protected ElementContext $elementContext;

    public function __construct(string $nodeName = Field::NODE_INPUT, ?string $nodeType = Field::TYPE_TEXT)
    {
        [$this->nodeName, $this->nodeType] = (new FormUtils())->regulateElementPrototype($nodeName, $nodeType);
        $this->elementContext = new ElementContext($this);
    }

    protected function swapField(UssElement $widgetContainer): void
    {
        $wrapper = $this->elementContext->gadgetWrapper->getElement();
        $wrapper->appendChild($widgetContainer);
    }
}
