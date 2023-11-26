<?php

namespace Ucscode\UssForm\Abstraction;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Interface\UssFormFieldInterface;
use Ucscode\UssForm\UssForm;
use Ucscode\UssForm\Trait\Field\FieldContainerTrait;
use Ucscode\UssForm\Trait\Field\FieldInfoTrait;
use Ucscode\UssForm\Trait\Field\FieldLabelTrait;
use Ucscode\UssForm\Trait\Field\FieldRowTrait;
use Ucscode\UssForm\Trait\Field\FieldValidationTrait;
use Ucscode\UssForm\Trait\Field\FieldWidgetContainerTrait;
use Ucscode\UssForm\Trait\Field\FieldWidgetMinorTrait;
use Ucscode\UssForm\Trait\Field\FieldWidgetTrait;
use Ucscode\UssForm\Trait\UssFormFieldTrait;

abstract class AbstractUssFormField implements UssFormFieldInterface
{
    use FieldRowTrait;
    use FieldContainerTrait;
    use FieldInfoTrait;
    use FieldLabelTrait;
    use FieldWidgetContainerTrait;
    use FieldWidgetTrait;
    use FieldWidgetMinorTrait;
    use FieldValidationTrait;
    use UssFormFieldTrait;

    /**
     * @method __constuct
     */
    public function __construct(
        public readonly string $nodeName = UssForm::NODE_INPUT,
        protected ?string $nodeType = UssForm::TYPE_TEXT
    ) {
        $this->generateElements();
        $this->buildFieldStructure();
    }

    /**
     * @method __debugInfo
     */
    public function __debugInfo()
    {
        $debugger = [];
        $skip = [];

        foreach((new \ReflectionClass($this))->getProperties() as $property) {
            $property->setAccessible(true);
            $name = $property->getName();
            if(!in_array($name, $skip)) {
                $value = $property->getValue($this);
                if($value instanceof UssElement) {
                    $value = 'object(' . $value::class . ')';
                } elseif($name === 'widgetOptions') {
                    $value = $value['values'];
                }
                $debugger[$name] = $value;
            }
        }

        return $debugger;
    }
}
