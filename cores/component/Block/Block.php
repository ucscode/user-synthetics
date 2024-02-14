<?php

namespace Uss\Component\Block;

class Block
{
    public readonly bool $isPermanent;
    protected array $templates = [];
    protected array $contents = [];

    public function __construct(bool $permanent = false)
    {
        // Once permanent blocks are registered, they cannot be detached
        $this->isPermanent = $permanent;
    }

    public function addTemplate(string $name, BlockTemplate $template): self
    {
        $this->templates[$name] = $template;
        return $this;
    }

    public function getTemplate(string $name): ?BlockTemplate
    {
        return $this->templates[$name] ?? null;
    }

    public function removeTemplate(string $name): self
    {
        if(!empty($this->templates[$name])) {
            unset($this->templates[$name]);
        }
        return $this;
    }

    public function getTemplates(): array
    {
        return $this->templates;
    }

    public function addContent(string $name, ?string $content, int $proprity = 0): self
    {
        $this->contents[$name] = [
            'content' => $content, 
            'priority' => $proprity
        ];
        return $this;
    }

    public function getContent(string $name): ?string
    {
        return ($this->contents[$name] ?? [])['content'] ?? null;
    }

    public function removeContent(string $name): self
    {
        if(!empty($this->contents[$name])) {
            unset($this->contents[$name]);
        }
        return $this;
    }

    public function getContents(): array
    {
        return $this->contents;
    }
}