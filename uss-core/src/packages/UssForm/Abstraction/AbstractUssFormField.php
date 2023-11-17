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
    use FieldValidationTrait;
    use UssFormFieldTrait;

    public readonly string $widgetId;
    private static int $count = 0;

    /**
     * @method __constuct
     */
    public function __construct(
        public readonly string $nodeName = UssElement::NODE_INPUT,
        protected ?string $nodeType = UssForm::TYPE_TEXT
    ) {
        $this->widgetId = $this->generateId();
        $this->generateElements();
    }

    /**
     * @method generateId
     */
    public function generateId(): string
    {
        self::$count++;
        $id = strtolower('uff-' . $this->nodeName . '-0' . self::$count);
        return $id;
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
