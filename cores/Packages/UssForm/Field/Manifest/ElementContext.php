<?php

namespace Ucscode\UssForm\Field\Manifest;

use Ucscode\UssForm\Resource\Context\Context;
use Ucscode\UssForm\Resource\Context\AbstractContext;
use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Field\Context\WidgetContext;
use Ucscode\UssForm\Field\Element\ContainerHandler;
use Ucscode\UssForm\Field\Element\FrameHandler;
use Ucscode\UssForm\Field\Element\InfoHandler;
use Ucscode\UssForm\Field\Element\LabelHandler;
use Ucscode\UssForm\Field\Element\ValidationHandler;
use Ucscode\UssForm\Field\Element\WidgetHandler;
use Ucscode\UssForm\Field\Element\WrapperHandler;
use Ucscode\UssForm\Field\Field;

/**
 * An ElementContext is a container that holds multiple predefined "Context" Object
 *
 * ElementContext for FIELD
 */
class ElementContext
{
    public readonly WidgetContext $widget;
    public readonly Context $frame;
    public readonly Context $wrapper;
    public readonly Context $label;
    public readonly Context $info;
    public readonly Context $container;
    public readonly Context $validation;

    public function __construct(public readonly Field $field)
    {
        $this->widget = new WidgetContext(
            $this->field->nodeName,
            new WidgetHandler($this)
        );

        $this->frame = new Context(
            UssElement::NODE_DIV,
            new FrameHandler($this)
        );
        
        $this->wrapper = new Context(
            UssElement::NODE_DIV,
            new WrapperHandler($this)
        );
        
        $this->label = new Context(
            UssElement::NODE_LABEL,
            new LabelHandler($this)
        );
        
        $this->info = new Context(
            UssElement::NODE_DIV,
            new InfoHandler($this)
        );

        $this->container = new Context(
            UssElement::NODE_DIV,
            new ContainerHandler($this)
        );
        
        $this->validation = new Context(
            UssElement::NODE_DIV,
            new ValidationHandler($this)
        );
    }
}
