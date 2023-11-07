<?php

/**
 * Extend the content within a twig block from outside the twig template
 *
 * This class allows you to add or remove content from a twig block without editing the twig theme
 * or creating a child theme.
 */
class BlockManager
{
    use SingletonTrait;

    private static array $blocks = [];

    /**
     * Insert content into a block without editing the twig template directory
     *
     * Example: appendTo('body', 'custom-name', 'value')
     *
     * Example2: appendTo('body', [
     *  'custom-name' => 'value'
     * ])
     */
    public function appendTo(string $blockName, array|string $identifier, ?string $content = null): self
    {
        if(!is_array($identifier)) {
            $identifier = [ $identifier => $content ];
        };
        $this->createBlock($blockName, $identifier);
        return $this;
    }

    /**
     * Remove content from a block
     *
     * Example: removeFrom('body', 'custom-name')
     *
     * Example2: removeFrom('body', [
     *  'custom-name'
     * ])
     */
    public function removeFrom(string $blockName, array|string $identifier): self
    {
        if(!is_array($identifier)) {
            $identifier = [ $identifier ];
        }
        $identifier = array_values($identifier);
        $this->filterBlock($blockName, $identifier);
        return $this;
    }

    /**
     * Clear all appended content of a block
     *
     * Example: clear('body');
     */
    public function clear(string $blockName): void
    {
        if(isset(self::$blocks[$blockName])) {
            unset(self::$blocks[$blockName]);
        };
    }

    /**
     * Get all content appended to a block
     *
     * Example: getBlocks('body')
     */
    public function getBlocks(?string $name = null): ?array
    {
        if($name === null) {
            return self::$blocks;
        };
        return self::$blocks[$name] ?? null;
    }

    /**
     * Order block content
     * @method order
     */
    public function order(string $blockName, array $identifierNames): void
    {

    }

    /**
     * @method createBlock
     */
    private function createBlock(string $name, array $data): void
    {
        $blocks = &self::$blocks;
        if(!isset($blocks[$name])) {
            $blocks[$name] = [];
        };
        $blocks[$name] = array_merge($blocks[$name], $data);
    }

    /**
     * @metho filterBlock
     */
    private function filterBlock(string $name, array $data): void
    {
        $blocks = &self::$blocks;
        if(!isset($blocks[$name])) {
            return;
        };
        $blocks[$name] = array_filter($blocks[$name], function ($value, $key) use ($data) {
            return !in_array($key, $data);
        }, ARRAY_FILTER_USE_BOTH);
        if(empty($blocks[$name])) {
            unset($blocks[$name]);
        };
    }

}
