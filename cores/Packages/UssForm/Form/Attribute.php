<?php

namespace Ucscode\UssForm\Form;

class Attribute
{
    protected ?string $action = null;
    protected ?string $target = null;
    protected ?string $charset = null;
    protected ?string $method = 'GET';
    protected ?string $enctype = 'application/x-www-form-urlencoded';
    protected bool $autoComplete = false;

    public function __construct(protected ?string $name = null)
    {}

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setAction(?string $action): self
    {
        $this->action = $action;
        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setEnctype(?string $enctype): self
    {
        $this->enctype = $enctype;
        return $this;
    }

    public function getEnctype(): ?string
    {
        return $this->enctype;
    }

    public function setTarget(?string $target): self
    {
        $this->target = $target;
        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setAutoComplete(bool $autoComplete): self
    {
        $this->autoComplete = $autoComplete;
        return $this;
    }

    public function hasAutoComplete(): bool
    {
        return $this->autoComplete;
    }

    public function setCharset(string $charset): self
    {
        $this->charset = $charset;
        return $this;
    }

    public function getCharset(): ?string
    {
        return $this->charset;
    }
}