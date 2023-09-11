<?php
/**
 * Extend the content within a twig block from outside the twig template
 *
 * This class allows you to add or remove content from a twig block without editing the twig theme
 * or creating a child theme.
 */
class UssTwigBlockManager
{
    use SingletonTrait;

    private array $blocks = [];

    public function __construct()
    {

    }

    public function get(?string $name = null)
    {
        if($name === null) {
            return $this->blocks;
        };
        return $this->blocks[$name] ?? null;
    }

    /**
     *
     */
    public function appendTo(string $blockName, $resolver, ?string $content = null)
    {
        if(!is_array($resolver)) {
            if(!is_string($resolver)) {
                $type = gettype($resolver);
                throw new Exception(__METHOD__ . " #(Argument 2): Must be a string or array, {$type} given");
            };
            $resolver = [ $resolver => $content ];
        };
        $this->createBlock($blockName, $resolver);
        return $this;
    }

    public function removeFrom(string $blockName, $resolver)
    {
        if(!is_array($resolver)) {
            if(!is_string($resolver)) {
                $type = gettype($resolver);
                throw new Exception(__METHOD__ . " #(Argument 2): Must be a string or array, {$type} given");
            };
            $resolver = [ $resolver ];
        }
        $resolver = array_values($resolver);
        $this->filterBlock($blockName, $resolver);
        return $this;
    }

    public function clear(string $blockName)
    {
        if(isset($this->blocks[$blockName])) {
            unset($this->blocks[$blockName]);
        };
    }

    public function order(string $blockName, array $resolverNames)
    {

    }

    private function createBlock(string $name, array $data)
    {
        if(!isset($this->blocks[$name])) {
            $this->blocks[$name] = [];
        };
        $this->blocks[$name] = array_merge($this->blocks[$name], $data);
    }

    private function filterBlock(string $name, array $data)
    {
        if(!isset($this->blocks[$name])) {
            return;
        };
        $this->blocks[$name] = array_filter($this->blocks[$name], function ($value, $key) use ($data) {
            return !in_array($key, $data);
        }, ARRAY_FILTER_USE_BOTH);
        if(empty($this->blocks[$name])) {
            unset($this->blocks[$name]);
        };
    }

}
