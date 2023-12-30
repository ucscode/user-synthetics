<?php

namespace Ucscode\UssForm\Field\Manifest;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Context\FieldContext;
use Ucscode\UssForm\Field\Context\WidgetContext;
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
    public readonly FieldContext $label;
    public readonly FieldContext $frame;
    public readonly FieldContext $wrapper;
    public readonly FieldContext $info;
    public readonly FieldContext $container;
    public readonly FieldContext $validation;
    public readonly FieldContext $prefix;
    public readonly FieldContext $suffix;

    public function __construct(protected Field $field)
    {
        $this->widget = new WidgetContext(
            $this->field->nodeName,
            new WidgetResolver($this)
        );

        $this->frame = new FieldContext(
            UssElement::NODE_DIV,
            new FrameResolver($this)
        );

        $this->wrapper = new FieldContext(
            UssElement::NODE_DIV,
            new WrapperResolver($this)
        );

        $this->container = new FieldContext(
            UssElement::NODE_DIV,
            new ContainerResolver($this)
        );

        $this->label = new FieldContext(
            UssElement::NODE_LABEL,
            new LabelResolver($this)
        );

        $this->info = new FieldContext(
            UssElement::NODE_DIV,
            new InfoResolver($this)
        );

        $this->validation = new FieldContext(
            UssElement::NODE_DIV,
            new ValidationResolver($this)
        );

        $this->prefix = new FieldContext(
            UssElement::NODE_SPAN,
            new PrefixResolver($this)
        );

        $this->suffix = new FieldContext(
            UssElement::NODE_SPAN,
            new SuffixResolver($this)
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
