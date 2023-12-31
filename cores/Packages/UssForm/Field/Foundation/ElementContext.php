<?php

namespace Ucscode\UssForm\Field\Foundation;

use stdClass;
use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Context\FrameContext;
use Ucscode\UssForm\Field\Context\GadgetContext;
use Ucscode\UssForm\Field\Context\GadgetWrapperContext;
use Ucscode\UssForm\Field\Context\InfoContext;
use Ucscode\UssForm\Field\Context\ValidationContext;
use Ucscode\UssForm\Field\Context\WrapperContext;
use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Gadget\Context\ContainerContext;
use Ucscode\UssForm\Gadget\Context\LabelContext;
use Ucscode\UssForm\Gadget\Context\PrefixContext;
use Ucscode\UssForm\Gadget\Context\SuffixContext;
use Ucscode\UssForm\Gadget\Context\WidgetContext;
use Ucscode\UssForm\Resource\Context\AbstractElementContext;

/**
 * An ElementContext is a container that holds multiple predefined "Context" Object
 *
 * ElementContext for FIELD
 */
class ElementContext extends AbstractElementContext
{
    // Gadget Component;
    public readonly ContainerContext $container;
    public readonly WidgetContext $widget;
    public readonly LabelContext $label;
    public readonly PrefixContext $prefix;
    public readonly SuffixContext $suffix;

    // Field Component;
    public readonly FrameContext $frame;
    public readonly WrapperContext $wrapper;
    public readonly InfoContext $info;
    public readonly GadgetWrapperContext $gadgetWrapper;
    public readonly ValidationContext $validation;


    public function __construct(protected Field $field)
    {
        $store = new stdClass();
        $gadget = new GadgetContext($this, $store);

        $this->container = $gadget->container;
        $this->widget = $gadget->widget;
        $this->label = $gadget->label;
        $this->prefix = $gadget->prefix;
        $this->suffix = $gadget->suffix;

        $this->frame = new FrameContext($this, UssElement::NODE_DIV, $store);
        $this->wrapper = new WrapperContext($this, UssElement::NODE_DIV, $store);
        $this->info = new InfoContext($this, UssElement::NODE_DIV, $store);
        $this->gadgetWrapper = new GadgetWrapperContext($this, UssElement::NODE_DIV, $store);
        $this->validation = new ValidationContext($this, UssElement::NODE_DIV, $store);

        $this->assembleContextElements();
        $this->visualizeContextElements();
    }

    public function getField(): Field
    {
        return $this->field;
    }

    public function visualizeContextElements(): void
    {
        $hidden = $this->widget->isHidden() || $this->widget->isButton();
        $this->label->setDOMHidden($hidden);
        $this->info->setDOMHidden($hidden);
        $this->validation->setDOMHidden($hidden);
    }

    public function export(): string
    {
        return $this->frame->getElement()->getHTML(true);
    }

    protected function assembleContextElements(): void
    {
        $element = $this->getContextElements();

        $element['frame']->appendChild($element['wrapper']);
        $element['wrapper']->appendChild($element['info']);
        $element['wrapper']->appendChild($element['gadgetWrapper']);
        $element['gadgetWrapper']->appendChild($element['container']);
        $element['wrapper']->appendChild($element['validation']);
        $element['container']->appendChild($element['widget']);

        $this->widget->isCheckable() ?
            $element['container']->appendChild($element['label']) :
            $element['wrapper']->prependChild($element['label']);
    }
}
