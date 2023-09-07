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
    use EncapsulatedPropertyAccessTrait;

    #[Accessible]
    protected string $tagName;

    public function __construct(string $tagName)
    {
        $this->tagName = strtoupper(trim($tagName));
        $this->void = in_array($this->tagName, $this->voidTags);
    }

    public function isVoid(bool $void): self
    {
        # Void elements are those without closing tags (e.g link, image, br)
        $this->void = $void;
        return $this;
    }

    // Attribute Management

    public function hasAttribute(string $attr): bool
    {
        return isset($this->attributes[$attr]);
    }

    public function setAttribute(string $attr, ?string $value = null): self
    {
        $attr = $this->evaluate($attr);
        $this->attributes[$attr] = $this->slice($value);
        return $this;
    }

    public function hasAttributeValue(string $attr, string $value): bool
    {
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

    public function getAttribute(string $attr): ?string
    {
        if($this->hasAttribute($attr)) {
            return implode(" ", $this->attributes[$attr]);
        };
        return null;
    }

    public function addAttributeValue(string $attr, string $value): self
    {
        $attr = $this->evaluate($attr);
        $value = $this->slice($value);
        $merge = array_merge($this->attributes[$attr], $value);
        $this->attributes[$attr] = array_unique($merge);
        return $this;
    }

    public function removeAttributeValue(string $attr, string $value): self
    {
        $attr = $this->evaluate($attr);
        $value = $this->slice($value);
        $diff = array_diff($this->attributes[$attr], $value);
        $this->attributes[$attr] = $diff;
        return $this;
    }

    public function removeAttribute(string $attr): self
    {
        if(isset($this->attributes[$attr])) {
            unset($this->attributes[$attr]);
        }
        return $this;
    }

    public function setContent(string $content): self
    {
        array_walk($this->children, function ($child) {
            $child->setParent(null);
        });
        $this->children = [];
        $this->content = $content;
        return $this;
    }

    public function hasContent(): bool
    {
        return !is_null($this->content);
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    // Child Management

    public function appendChild(UssElementBuilder $child): void
    {
        $child = $this->scan($child, __METHOD__);
        $this->children[] = $child;
    }

    public function prependChild(UssElementBuilder $child): void
    {
        $child = $this->scan($child, __METHOD__);
        array_unshift($this->children, $child);
    }

    public function insertBefore(UssElementBuilder $child, UssElementBuilder $refNode): void
    {
        $key = array_search($refNode, $this->children, true);
        if($key === false) {
            return;
        };
        $child = $this->scan($child, __METHOD__);
        array_splice($this->children, $key, 0, [$child]);
    }

    public function insertAfter(UssElementBuilder $child, UssElementBuilder $refNode): void
    {
        $key = array_search($refNode, $this->children, true);
        if($key === false) {
            return;
        }
        $child = $this->scan($child, __METHOD__);
        array_splice($this->children, ($key + 1), 0, [$child]);
    }

    public function replaceChild(UssElementBuilder $child, UssElementBuilder $refNode): void
    {
        $key = array_search($refNode, $this->children, true);
        if($key === false) {
            return;
        }
        $child = $this->scan($child, __METHOD__);
        $this->children[$key] = $child;
    }

    public function firstChild(): ?UssElementBuilder
    {
        return $this->children[0] ?? null;
    }

    public function lastChild(): ?UssElementBuilder
    {
        $index = count($this->children) - 1;
        return $this->children[$index] ?? null;
    }

    public function getChild(int $index): ?UssElementBuilder
    {
        return $this->children[$index] ?? null;
    }

    public function removeChild(UssElementBuilder $child): void
    {
        $key = array_search($child, $this->children, true);
        if($key !== false) {
            unset($this->children[$key]);
            $this->children = array_values($this->children);
        };
    }

    public function getHTML(bool $indent = false): string
    {
        $index = $indent ? 0 : null;
        $html = $this->buildNode($this, $index);
        return $html;
    }

    protected function setParent(UssElementBuilder $parent)
    {
        $this->parentElement = $parent;
    }


    // Helper Methods

    private function evaluate(string $attr)
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
        $key = array_search($child, $this->children, true);
        if($key !== false) {
            array_splice($this->children, $key, 1);
            $this->children = array_values($this->children);
        };
        $child->setParent($this);
        return $child;
    }

}
