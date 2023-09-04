<?php

abstract class AbstractUssElementParser
{
    protected $parentElement;
    
    protected $child = [];

    protected $voidTags = [
        'area',
        'base',
        'br',
        'col',
        'embed',
        'hr',
        'img',
        'input',
        'link',
        'meta',
        'param',
        'source',
        'track',
        'wbr'
    ];

    public function __debugInfo()
    {
        $debugInfo = [];
        $skip = ['parentElement', 'voidTags'];
        foreach ($this as $property => $value) {
            if(in_array($property, $skip)) {
                continue;
            } elseif($property == 'child') {
                $value = count($value);
            }
            $debugInfo[$property] = $value;
        }
        return $debugInfo;
    }

    public function find(string $selectors)
    {
        $nodelist = [];

        /**
         * Multiple selectors are seperated by comma:
         *
         * Example: h1, h2.class, div[id]
         *
         * We have to split the selector by comma and get each distinction
         */
        $distinctSelector = explode(",", $selectors);

        # Loop the selector

        foreach($distinctSelector as $selector) {

            /**
             * Selectors can be quite complex for just a single element, For example:
             *
             * .span[data-name='money maker'][class=54] h1 .work[id]
             *
             * Now we have to split the selector by space
             */
            $selector = $this->splitSelector($selector);

            # Now, we have to breakdown the selector into an array representing tags and attributes
            
            $collapsedSelector = $this->collapseSelector($selector);
            
            # Now, let's used the collapsed selector to find elements that match the selector

            $results = $this->seek($this->child, $collapsedSelector);

            foreach($results as $node) {
                $key = array_search($node, $nodelist, true);
                if($key === false) {
                    $nodelist[] = $node;
                }
            }

        };

        return $nodelist;

    }

    protected function buildNode(UssElementBuilder $node, ?int $indent)
    {
        $nodename = $node->tagName;
        $attributes = [];

        foreach($node->attributes as $key => $values) {
            $attributes[] = $key . "=\"" . htmlentities(implode(" ", $values)) . "\"";
        }
        
        if(!is_null($indent)) {
            $indentation = str_repeat("\t", $indent);
            $carriage = "\n";
        } else {
            $indentation = $carriage = null;
        }

        $html = $indentation . "<" . $nodename;
        
        if(!empty($attributes)) {
            $html .= " " . implode(" ", $attributes);
        };

        if(!$node->void) {
            
            $html .= ">" . $carriage;

            foreach($node->child as $child) {
                if(is_null($indent)) {
                    $index = null;
                } else {
                    $index = $indent + 1;
                }
                $html .= $this->buildNode($child, $index) . $carriage;
            }

            $html .= $indentation . "</" . $nodename . ">";

        } else {

            $html .= "/>";
        
        }

        return $html;
    }

    protected function slice(?string $value = null)
    {
        if(is_null($value)) {
            $value = '';
        };
        $value = array_filter(array_map('trim', explode(' ', $value)));
        return $value;
    }

    # Finder

    private function seek(array $children, array $collapsedSelector)
    {
        # For each match, a selector key will be shifted so we must retain accurate numeric keys
        $collapsedSelector = array_values($collapsedSelector);
        
        # An array to store matched nodes
        $capturedNodes = [];

        if(!empty($collapsedSelector)) {

            foreach($children as $node) {

                $copySelector = $collapsedSelector;

                /**
                 * A node must match everything on the first index of the selector before it can be captured
                 */
                $match = $this->matchSelectorNode($node, $copySelector[0]);

                if($match) {

                    // Remove the matched selector and proceed to it's child node
                    array_shift($copySelector);

                    // The last element in the selector list is the final element that selectors point to
                    if(empty($copySelector)) {
                        $capturedNodes[] = $node;
                    };

                };

                $capturedNodes = array_merge($capturedNodes, $this->seek($node->child, $copySelector));

            };

        };

        return $capturedNodes;

    }

    private function splitSelector(string $selector): array
    {
        /**
         * Consider a selector like this -> "span[class='mb-4 rounded'] h1"
         *
         * There is a space between the single quote and between another selector.
         * Using explode() function will split the string in an undesired manner
         * Thus:
         */

        # Create a fake context to temporarily replace space around attributes

        $spacer = '!' . md5(rand()) . '!';

        $selector = preg_replace_callback('/\[[^\]]+\]/', function ($match) use ($spacer) {
            return str_replace(' ', $spacer, $match[0]);
        }, $selector);

        # Now split the CSS Selector

        $selector = explode(' ', $selector);

        # Now remove the context that was used to represent space earlier

        $selector = array_map(function ($value) use ($spacer) {
            return str_replace($spacer, ' ', $value);
        }, $selector);

        return array_values($selector);

    }

    # Break down selector into array
    private function collapseSelector(array $selectors): array
    {
        $collapse = [];
        foreach($selectors as $selector) {
            $collapse[] = $this->parseSelector($selector);
        };
        return array_values(array_filter($collapse));
    }

    private function parseSelector(string $selector): array
    {
        $result = [
            'tagname' => null
        ];

        if(preg_match_all('/(\.|#|\[)?[^.#\[]+/', $selector, $matches)) {

            foreach($matches[0] as $unit) {

                $type = substr($unit, 0, 1);

                if($type === '.') {

                    if(!isset($result['class'])) {
                        $result['class'] = [];
                    };
                    $result['class'][] = substr($unit, 1);
                    
                } elseif($type === '#') {

                    if(!isset($result[''])) {
                        $result['id'] = [];
                    }
                    $result['id'][] = substr($unit, 1);

                } elseif($type === '[') {

                    $attrs = array_map('trim', explode("=", substr($unit, 1, -1)));
                    $key = $attrs[0];
                    $value = $attrs[1] ?? null;
                    $value = !empty($value) ? explode(' ', trim($value, "'\"")) : [];

                    if(isset($result[$key])) {
                        $result[$key] = array_merge($result[$key], $value);
                    } else {
                        $result[$key] = $value;
                    }

                } else {

                    $result['tagname'] = $unit;

                }

            };

        };

        return array_filter($result, function($value) {
            return $value !== null;
        });

    }

    private function matchSelectorNode(UssElementBuilder $node, array $selector)
    {
        $matches = [];

        if(!empty($selector['tagname'])) {
            $matches[] = strtolower($selector['tagname']) === $node->tagName;
        };

        unset($selector['tagname']);

        # Match Properties
        foreach($selector as $attr => $values) {
            if($node->hasAttribute($attr)) {
                $matches[] = true;
                foreach($values as $value) {
                    $matches[] = $node->hasProperty($attr, $value);
                }
            } else {
                $matches[] = false;
            };
        };
        
        return !in_array(false, $matches) && !empty($matches);
    }

}
