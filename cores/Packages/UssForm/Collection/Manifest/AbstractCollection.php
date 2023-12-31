<?php

namespace Ucscode\UssForm\Collection\Manifest;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Collection\Foundation\ElementContext;
use Ucscode\UssForm\Field\Field;
use Ucscode\UssForm\Resource\Service\FormUtils;

abstract class AbstractCollection implements CollectionInterface
{
    protected array $fields = [];
    protected ElementContext $elementContext;
    protected static int $fieldIndex = 1;

    public function __construct()
    {
        $this->elementContext = new ElementContext($this);
    }

    protected function swapField(UssElement $fieldElement, ?UssElement $oldFieldElement): void
    {
        $collectionContainer = $this->elementContext->container->getElement();
        $oldFieldElement ?
            $collectionContainer->replaceChild($fieldElement, $oldFieldElement) :
            $collectionContainer->appendChild($fieldElement);
    }

    protected function welcomeField(string $name, Field $field): void
    {
        $context = $field->getElementContext();

        if(!$context->isFixed()) {

            // Update Widget Context
            if(!$context->widget->isFixed()) {

                !$context->widget->hasAttribute('name') ?
                    $context->widget->setAttribute('name', $name) : null;

                !$context->widget->hasAttribute('id') ?
                    $context->widget->setAttribute('id', 'field-widget-' . self::$fieldIndex++) : null;
            }

            // Update Label Context
            if(!$context->label->isFixed()) {

                !$context->label->hasValue() ?
                    $context->label->setValue((new FormUtils())->capitalizeContent($name)) : null;

                !$context->label->hasAttribute('for') && $context->widget->hasAttribute('id') ?
                    $context->label->setAttribute('for', $context->widget->getAttribute('id')) : null;
            }
        }
    }
}
