<?php

namespace Ucscode\UssForm\Field\Manifest;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Context\ContainerContext;
use Ucscode\UssForm\Field\Context\FieldContext;
use Ucscode\UssForm\Field\Context\FrameContext;
use Ucscode\UssForm\Field\Context\InfoContext;
use Ucscode\UssForm\Field\Context\LabelContext;
use Ucscode\UssForm\Field\Context\PrefixContext;
use Ucscode\UssForm\Field\Context\SuffixContext;
use Ucscode\UssForm\Field\Context\ValidationContext;
use Ucscode\UssForm\Field\Context\WidgetContext;
use Ucscode\UssForm\Field\Context\WrapperContext;
use Ucscode\UssForm\Field\Element\ContainerResolver;
use Ucscode\UssForm\Field\Element\FrameResolver;
use Ucscode\UssForm\Field\Element\InfoResolver;
use Ucscode\UssForm\Field\Element\LabelResolver;
use Ucscode\UssForm\Field\Element\PrefixResolver;
use Ucscode\UssForm\Field\Element\SuffixResolver;
use Ucscode\UssForm\Field\Element\ValidationResolver;
use Ucscode\UssForm\Field\Element\WidgetResolver;
use Ucscode\UssForm\Field\Element\WrapperResolver;
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
        $this->widget = new WidgetContext(
            $this, 
            $this->field->nodeName
        );

        $this->frame = new FrameContext(
            $this, 
            UssElement::NODE_DIV
        );

        $this->wrapper = new WrapperContext(
            $this, 
            UssElement::NODE_DIV
        );

        $this->container = new ContainerContext(
            $this, 
            UssElement::NODE_DIV
        );

        $this->label = new LabelContext(
            $this, 
            UssElement::NODE_LABEL
        );

        $this->info = new InfoContext(
            $this, 
            UssElement::NODE_DIV
        );

        $this->validation = new ValidationContext(
            $this, 
            UssElement::NODE_DIV
        );

        $this->prefix = new PrefixContext(
            $this, 
            UssElement::NODE_SPAN
        );

        $this->suffix = new SuffixContext(
            $this, 
            UssElement::NODE_SPAN
        );

        $this->groupContextElements();
    }

    public function getField(): Field
    {
        return $this->field;
    }

    protected function groupContextElements(): void
    {
        $element = $this->getContextElements();

        $element['frame']->appendChild($element['wrapper']);
        $element['wrapper']->appendChild($element['info']);
        $element['wrapper']->appendChild($element['container']);
        $element['wrapper']->appendChild($element['validation']);
        $element['container']->appendChild($element['widget']);

        if(!$this->widget->isButton() && !$this->widget->isHidden()) {
            $this->widget->isCheckable() ?
                $element['container']->appendChild($element['label']) :
                $element['wrapper']->prependChild($element['label']);
        } else {
            $this->label->setDOMHidden(true);
            $this->info->setDOMHidden(true);
            $this->validation->setDOMHidden(true);
        }
    }
}
