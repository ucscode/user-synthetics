<?php
/**
 * Uss Element Builder
 *
 * Basically, DOM Elements are build with tags (such as h1, p, ...) and attributes (class=, style=).
 * Then they have children and parent. That's all!
 * Any other component added to DOM are features to make it more awesome.
 * This class focuses on the basics of building tags, adding attributes and assigning it to another element
 */
class UssElementBuilder extends AbstractUssElementParser
{
    use ProtectedPropertyAccessTrait;

    protected string $tagName;
    protected $attributes = [];
    protected $parentElement;
    protected $child = [];

    public function __construct(string $tagName)
    {
        $this->tagName = strtoupper($tagName);
    }

    // Attribute Management

    public function hasAttribute(string $attr) {
        return isset($this->attributes[$attr]);
    }

    public function setAttribute(string $attr, ?string $value = null)
    {
        $attr = $this->write($attr);
        $this->attributes[$attr] = $this->slice($value);
        return $this;
    }

    public function hasProperty(string $attr, string $value) {
        if(!$this->hasAttribute($attr)) {
            return false;
        };
        $value = $this->slice($value);
        foreach($value as $unit) {
            if(!in_array($unit, $this->attributes[$attr])) {
                return false;
            };      
        };
        return !empty($value);
    }

    public function addProperty(string $attr, string $value)
    {
        $attr = $this->write($attr);
        $value = $this->slice($value);
        $merge = array_merge($this->attributes[$attr], $value);
        $this->attributes[$attr] = array_unique($merge);
        return $this;
    }

    public function removeProperty(string $attr, string $value)
    {
        $attr = $this->write($attr);
        $value = $this->slice($value);
        $diff = array_diff($this->attributes[$attr], $value);
        $this->attributes[$attr] = $diff;
        return $this;
    }

    public function unsetAttribute(string $attr)
    {
        if(isset($this->attributes[$attr])) {
            unset($this->attributes[$attr]);
        }
        return $this;
    }

    // Child Management

    public function appendChild(UssElementBuilder $child)
    {
        $child = $this->scan($child, __METHOD__);
        $this->child[] = $child;
    }

    public function prependChild(UssElementBuilder $child)
    {
        $child = $this->scan($child, __METHOD__);
        array_unshift($this->child, $child);
    }

    public function insertBefore(UssElementBuilder $child, UssElementBuilder $refNode)
    {
        $key = array_search($refNode, $this->child, true);
        if($key === false) {
            return;
        };
        $child = $this->scan($child, __METHOD__);
        array_splice($this->child, $key, 0, [$child]);
    }

    public function insertAfter(UssElementBuilder $child, UssElementBuilder $refNode)
    {
        $key = array_search($refNode, $this->child, true);
        if($key === false) {
            return;
        }
        $child = $this->scan($child, __METHOD__);
        array_splice($this->child, ($key + 1), 0, [$child]);
    }

    public function replaceChild(UssElementBuilder $child, UssElementBuilder $refNode)
    {
        $key = array_search($refNode, $this->child, true);
        if($key === false) {
            return;
        }
        $child = $this->scan($child, __METHOD__);
        $this->child[$key] = $child;
    }

    public function getHTML() {
        $html = $this->buildNode($this);
        echo $html;
    }

    protected function setParent(UssElementBuilder $parent)
    {
        $this->parentElement = $parent;
    }


    // Helper Methods

    private function write(string $attr)
    {
        $attr = str_replace(" ", '', $attr);
        if(!isset($this->attributes[$attr])) {
            $this->attributes[$attr] = [];
        };
        return $attr;
    }

    private function scan(UssElementBuilder $child, string $method): UssElementBuilder
    {
        if($this === $child) {
            throw new Exception("Trying to add self as child in " . $method);
        };
        $key = array_search($child, $this->child, true);
        if($key !== false) {
            array_splice($this->child, $key, 1);
            $this->child = array_values($this->child);
        };
        $child->setParent($this);
        return $child;
    }

}
