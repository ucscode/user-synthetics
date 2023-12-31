<?php

namespace Ucscode\UssForm\Gadget;

use stdClass;
use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Gadget\Context\ContainerContext;
use Ucscode\UssForm\Gadget\Context\LabelContext;
use Ucscode\UssForm\Gadget\Context\PrefixContext;
use Ucscode\UssForm\Gadget\Context\SuffixContext;
use Ucscode\UssForm\Gadget\Context\WidgetContext;
use Ucscode\UssForm\Gadget\Manifest\AbstractGadget;
use Ucscode\UssForm\Resource\Service\FormUtils;

// In this case, Gadget is the ElementContext

class Gadget extends AbstractGadget
{
    public readonly string $nodeName;
    public readonly ?string $nodeType;
    public readonly LabelContext $label;
    public readonly ContainerContext $container;
    public readonly WidgetContext $widget;
    public readonly PrefixContext $prefix;
    public readonly SuffixContext $suffix;
    protected stdClass $store;

    public function __construct(string $nodeName = Field::NODE_INPUT, ?string $nodeType = Field::TYPE_TEXT)
    {
        [$this->nodeName, $this->nodeType] = (new FormUtils())->regulateElementPrototype($nodeName, $nodeType);

        $this->store = new stdClass();

        $this->widget = new WidgetContext(
            $this, 
            $this->store
        );

        $this->label = new LabelContext(
            $this, 
            UssElement::NODE_LABEL, 
            $this->store
        )
        ;
        $this->container = new ContainerContext(
            $this, 
            UssElement::NODE_DIV, 
            $this->store
        );

        $this->prefix = new PrefixContext(
            $this, 
            UssElement::NODE_SPAN, 
            $this->store
        );

        $this->suffix = new SuffixContext(
            $this, 
            UssElement::NODE_SPAN, 
            $this->store
        );

        $this->assembleContextElements();
    }

    public function export(): string
    {
        return $this->container->getElement()->getHTML(true);
    }

    public function visualizeContextElements(): void
    {
        
    }

    protected function assembleContextElements(): void
    {
        $elements = $this->getContextElements();
        $elements['container']->appendChild($elements['widget']);
        if($this->widget->isCheckable()) {
            $elements['container']->appendChild($elements['label']);
        }
    }
}