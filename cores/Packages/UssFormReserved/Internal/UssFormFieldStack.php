<?php

namespace Ucscode\UssForm\Internal;

use Ucscode\UssElement\UssElement;
use Ucscode\UssForm\Abstraction\AbstractUssFormFieldStack;
use Ucscode\UssForm\Trait\Fieldstack\FieldstackContainerTrait;
use Ucscode\UssForm\Trait\Fieldstack\FieldstackElementTrait;
use Ucscode\UssForm\Trait\Fieldstack\FieldstackFieldTrait;
use Ucscode\UssForm\Trait\Fieldstack\FieldstackInstructionTrait;
use Ucscode\UssForm\Trait\Fieldstack\FieldstackSubtitleTrait;
use Ucscode\UssForm\Trait\Fieldstack\FieldstackTitleTrait;

class UssFormFieldStack extends AbstractUssFormFieldStack
{
    use FieldstackFieldTrait;
    use FieldstackElementTrait;
    use FieldstackContainerTrait;
    use FieldstackTitleTrait;
    use FieldstackSubtitleTrait;
    use FieldstackInstructionTrait;

    /**
     * @method disableOuterContainer
     */
    public function setFieldStackDisabled(bool $status): self
    {
        if($status) {
            $this->outerContainer['element']->setAttribute('disabled', 'disabled');
        } else {
            $this->outerContainer['element']->removeAttribute('disabled');
        }
        return $this;
    }

    /**
     * @method isFieldStackDisabled
     */
    public function isFieldStackDisabled(): bool
    {
        return $this->outerContainer['element']->hasAttribute('disabled');
    }

    /**
     * @method getFieldStackAsElement
     */
    public function getFieldStackAsElement(): UssElement
    {
        $this->refactorFieldstackElement();
        return $this->outerContainer['element'];
    }

    /**
     * @method getFieldStackAsHTML
     */
    public function getFieldStackAsHTML(): string
    {
        return $this->getFieldStackAsElement()->getHTML(true);
    }

    /**
     * @method refactorFieldstackElements
     */
    protected function refactorFieldstackElement(): void
    {
        $optional = ['title', 'subtitle', 'instruction'];
        foreach($optional as $prop) {
            $property = $this->{$prop};
            if(is_null($property['value'])) {
                $this->hideElement($property['element']);
            }
        }
    }
}
