<?php

namespace Ucscode\UssForm\Form;

use Exception;

class Attribute
{
    protected ?Form $form = null;
    protected ?string $action = null;
    protected ?string $target = null;
    protected ?string $charset = null;
    protected ?string $enctype = null;
    protected ?string $autoComplete = null;
    protected ?string $method = 'GET';

    public function __construct(protected ?string $name = null)
    {}

    public function setName(?string $name): self
    {
        $this->name = $name;
        $this->bind('name', $name);
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setAction(?string $action): self
    {
        $this->action = $action;
        $this->bind('action', $action);
        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;
        $this->bind('method', $method);
        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setEnctype(?string $enctype): self
    {
        $this->enctype = $enctype;
        $this->bind('enctype', $enctype);
        return $this;
    }

    public function getEnctype(): ?string
    {
        return $this->enctype;
    }

    public function setTarget(?string $target): self
    {
        $this->target = $target;
        $this->bind('target', $target);
        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setAutoComplete(?string $autoComplete): self
    {
        $this->autoComplete = $autoComplete;
        $this->bind('autocomplete', $autoComplete);
        return $this;
    }

    public function getAutoComplete(): ?string
    {
        return $this->autoComplete;
    }

    public function setCharset(?string $charset): self
    {
        $this->charset = $charset;
        $this->bind("accept-charset", $charset);
        return $this;
    }

    public function getCharset(): ?string
    {
        return $this->charset;
    }

    public function defineFormInstanceOnce(Form $form): void
    {
        if($this->form) {
            throw new Exception(
                "Form Instance cannot be defined more than once for an Attribute instance"
            );
        }
        
        $this->form = $form;

        $this->setName($this->name);
        $this->setMethod($this->method);
        $this->setAction($this->action);
        $this->setEnctype($this->enctype);
        $this->setCharset($this->charset);
        $this->setAutoComplete($this->autoComplete);
    }

    protected function bind(string $name, ?string $value): void
    {
        $this->form && !empty($value) ?
            $this->form->getElement()->setAttribute($name, $value) : null;
    }
}