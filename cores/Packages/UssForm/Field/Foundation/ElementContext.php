<?php

namespace Ucscode\UssForm\Field\Foundation;

use stdClass;
use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Context\ContainerContext;
use Ucscode\UssForm\Field\Context\FrameContext;
use Ucscode\UssForm\Field\Context\InfoContext;
use Ucscode\UssForm\Field\Context\LabelContext;
use Ucscode\UssForm\Field\Context\PrefixContext;
use Ucscode\UssForm\Field\Context\SuffixContext;
use Ucscode\UssForm\Field\Context\ValidationContext;
use Ucscode\UssForm\Field\Context\WidgetContext;
use Ucscode\UssForm\Field\Context\WrapperContext;
use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Resource\Context\AbstractElementContext;

/**
 * An ElementContext is a container that holds multiple predefined "Context" Object
 *
 * ElementContext for FIELD
 */
class ElementContext extends AbstractElementContext
{
    public readonly WidgetContext $widget;
    public readonly LabelContext $label;
    public readonly FrameContext $frame;
    public readonly WrapperContext $wrapper;
    public readonly InfoContext $info;
    public readonly ContainerContext $container;
    public readonly ValidationContext $validation;
    public readonly PrefixContext $prefix;
    public readonly SuffixContext $suffix;

    public function __construct(protected Field $field)
    {
        $store = new stdClass();

        $this->widget = new WidgetContext(
            $this,
            $store
        );

        $this->frame = new FrameContext(
            $this,
            UssElement::NODE_DIV,
            $store
        );

        $this->wrapper = new WrapperContext(
            $this,
            UssElement::NODE_DIV,
            $store
        );

        $this->container = new ContainerContext(
            $this,
            UssElement::NODE_DIV,
            $store
        );

        $this->label = new LabelContext(
            $this,
            UssElement::NODE_LABEL,
            $store
        );

        $this->info = new InfoContext(
            $this,
            UssElement::NODE_DIV,
            $store
        );

        $this->validation = new ValidationContext(
            $this,
            UssElement::NODE_DIV,
            $store
        );

        $this->prefix = new PrefixContext(
            $this,
            UssElement::NODE_SPAN,
            $store
        );

        $this->suffix = new SuffixContext(
            $this,
            UssElement::NODE_SPAN,
            $store
        );

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
        $element['wrapper']->appendChild($element['container']);
        $element['wrapper']->appendChild($element['validation']);
        $element['container']->appendChild($element['widget']);

        $this->widget->isCheckable() ?
            $element['container']->appendChild($element['label']) :
            $element['wrapper']->prependChild($element['label']);
    }
}
