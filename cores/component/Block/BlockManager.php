<?php

namespace Uss\Component\Block;

use Exception;
use Uss\Component\Trait\SingletonTrait;

/**
 * Extend the content within a twig block from outside the twig template
 *
 * This class allows you to add or remove content from a twig block without editing the twig theme
 * or creating a child theme.
 */
class BlockManager
{
    use SingletonTrait;

    protected array $blocks = [];

    public function addBlock(string $name, Block $block = new Block()): self
    {
        $pseudo = $this->getBlock($name);
        if($pseudo && $pseudo->isPermanent) {
            throw new Exception("Cannot override permanent block: {$name}");
        }
        $this->blocks[$name] = $block;
        return $this;
    }

    public function getBlock($name): ?Block
    {
        return $this->blocks[$name] ?? null;
    }

    public function removeBlock(string $name): ?Block
    {
        $block = $this->getBlock($name);
        if($block) {
            if($block->isPermanent) {
                throw new Exception("Cannot remove permanent block: {$name}");
            }
            unset($this->blocks[$name]);
        }
        return $block;
    }
}
