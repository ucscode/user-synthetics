<?php

namespace Ucscode\Packages;

use Gt\CssXPath\Translator;

/**
 * X2Client - HTML to Email-compatible Table Converter
 *
 * The X2Client class is a PHP utility that converts HTML-related syntax into a table structure
 * compatible with email clients. It aims to provide a compact and cross-platform solution for
 * generating HTML emails that render consistently across different email clients.
 * It offers functionality to convert internal CSS into inline CSS and adds necessary styles and
 * attributes to ensure email compatibility. This eliminates the frustration that often arises
 * when creating email templates.
 *
 * ### Example usage:
 *
 * ```php
 * $html_syntax = "<x2:body>...</x2:body>";
 * $X2Client = new X2Client($html_syntax);
 * echo $X2Client->render();
 * ```
 *
 * > Using the `x2` prefix on HTML syntax may no longer be necessary in future development or versions of X2Client
 *
 * @package   X2Client
 * @author    ucscode
 * @license   MIT
 * @link      https://github.com/ucscode/X2Client
 */
class X2Client
{
    /**
     * The namespace used accross X2Client
     *
     * @var string
     * @ignore
     */
    protected $namespace = "x2";

    /** @ignore */
    protected $domain = 'https://github.com/ucscode/X2Client';
    /** @ignore */
    protected $dom;
    /** @ignore */
    protected $hashTag;
    /** @ignore */
    protected $errors;
    /** @ignore */
    protected $cssRules;
    /** @ignore */
    protected $XML;

    /**
     * Block level elements
     *
     * @var array
     * @ignore
     */
    protected $block = array(
        "div",
        "p"
    );

    /**
     * @ignore
     */
    protected $xpath;

    /**
     * X2Client constructor.
     *
     * Creates a new instance of the X2Client class with the provided HTML syntax.
     *
     * @param string $syntax The HTML syntax to be converted into an email-compatible table structure.
     */
    public function __construct(string $syntax)
    {

        // Prevent Output Of XML Error;

        libxml_use_internal_errors(true);

        # I'M NOT PERFECT! BUT I'LL TRY MY BEST TO HANDLE SOME BASIC XML ERRORS:
        # LET'S DO THIS

        /**
         * ERROR 1: EntityRef: expecting ';'
         *
         * SOLUTION: Replace `&` with `&amp;` only if it is not a valid HTML Entity;
         */

        $syntax = preg_replace("/&(?!([\w\n]{2,7}|#[\d]{1,4});)/", "&amp;", $syntax);


        /**
         * ERROR 2: Opening and ending tag mismatch: ...
         *
         * SOLUTION: Replace self-closing tags such as `<br>` with `<br/>`
        */

        $syntax = $this->handleMismatchedTags($syntax);


        /**
         * ERROR 3: Entity 'x' not defined
         *
         * Where `x` represents an HTML entity such as `nbsp`, `mdash` etc;
         * These entities are valid for HTML but not valid for XML.
         *
         * So! No fix available :(
         *
         * Wait!!! According to my research in:
         * https://stackoverflow.com/questions/4645738/domdocument-appendxml-with-special-characters
         *
         * > The only character entities that have an "actual name" defined (instead of using a numeric reference) are:
         * - &amp;
         * - &lt;
         * - &gt;
         * - &quot
         * - &apos;
         *
         * That means you have to use the numeric equivalent...
         *
         * ---------------------------------------------------------
         *
         * SOLUTION: Let's get a list of all HTML entities and convert them to their numeric equivalence;
         *
         * Evil Laugh: He..He..He..
        */

        $syntax = $this->xmlentities($syntax);


        /**
         * ERROR 4: Error parsing attribute name
         *
         * I discovered this error when a CSS Comment was found on a style tag.
         * ## ERROR CAUSE!
         *
         * SOLUTION: Remove CSS Comment Tags
        */

        $expression = "~\/\*(.*?)(?=\*\/)\*\/~s";

        $syntax = trim(preg_replace($expression, '', $syntax));

        /**
         * Base on research, another problem came from using attribute value with a name
         *
         * `<cardId ="01">` instead of `<card Id="01">`
         *
         * SOLUTION: NONE! :(
         *
         * ### WHY?
         *
         * - Because We can't tell whether `cardId` is tag on it's own (since it's XML)
         * - We also cannot tell whether the separation should be `<car dId="01">` or `<cardI d="01">`
         *
         * Therefore, It's left for the developer to monitor the syntax and correct such error!
         */

        /*
            I'll try fixing more errors when I find them!
            Let Proceed...

            Finally, we have to remove the <!doctype html> declaration as we're moving to the XML Environment
        */

        $syntax = trim(preg_replace("#^<(?:x2:)?!doctype html>#", '', $syntax));


        /**
         * Now! Let's create a random `string` as a tag that we can use as the root element
         * On this root element, we declare our namespace
        */

        $this->hashTag = "_" . sha1(mt_rand());

        $xml = "
			<{$this->namespace}:{$this->hashTag} xmlns:{$this->namespace}='{$this->domain}'>
				{$syntax}
			</{$this->namespace}:{$this->hashTag}>
		";


        /** Now! Let's Create DOMDocument and load the XML String; */

        $this->dom = new DOMDocument("1.0", "utf-8");

        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = true;

        $this->XML = trim($xml);

        $this->dom->loadXML($this->XML);


        /**
         * Now! Let's get ready to make some advance search using DOMXPath;
         * Since DOMDocument doesn't use css ;)
        */

        $this->xpath = new DOMXPath($this->dom);
        $this->xpath->registerNamespace($this->{"namespace"}, $this->domain);

        /**
         * ## USAGE:
         *
         * ```php
         * $xPath = Translator( "css selector" );
         * DOMXPath::Query( $xPath );
         * ```
         *
         * Very simple!
         * Now! Let's capture the error.
         * So Incase the XML Doesn't Load, We can easily check what's causing the problem;
         *
        */

        $this->errors = libxml_get_errors();


        /**
         * After calling the:
         *
         * ```php
         * H2Client::__construct( $XML_STRING )
         * ```
         *
         * The next thing is to render:
         *
         * ```php
         * H2Client::render();
         * ```
        */

    }

    /**
     * X2Client magic __get method
     *
     * @method __get
     *
     * @param string $name
     *
     * @return mixed
     * @ignore
     */
    public function __get($name)
    {
        return $this->{$name} ?? null;
    }

    /**
     * With this method, we can try to close *self-closing* tags that are not properly closed!
     * Such as using `<br>` instead of `<br/>`
     *
     * @param string $syntax
     *
     * @return string
     * @ignore
     */
    protected function handleMismatchedTags(string $syntax)
    {

        $tags = array(
            "area",
            "base",
            "br",
            "col",
            "embed",
            "hr",
            "img",
            "input",
            "link",
            "meta",
            "param",
            "source",
            "track",
            "wbr"
        );

        foreach($tags as $tagname) {
            $expression = "~<((?:{$tagname}|{$this->namespace}:{$tagname})[^>]*)~";
            $syntax = preg_replace_callback($expression, function ($matches) {
                $tag = $matches[0];
                if(substr($tag, -1) != "/") {
                    $tag .= "/";
                }
                return $tag;
            }, $syntax);
        };

        return $syntax;

    }

    /**
     * Convert unrecognized HTML entities to their numeric equivalence
     *
     * Although this method was first created here, a better version was later cloned from `Core::xmlentities`
     *
     * @see Core::xmlentities
     *
     * @param string $string
     *
     * @return string
     * @ignore
     */
    protected function xmlentities(string $string = '')
    {

        /*
            The XML entities data was moved to " xhtml-entities.json " file due to the large number of character it contains.
            - Thus, absence of the file will result to false in converting HTML named entity to number entity;
        */

        $file = __DIR__ . '/xhtml-entities.json';
        if(!is_file($file)) {
            return false;
        }

        # Convert to JSON and validate the conversion;

        $content = json_decode(file_get_contents($file), true);
        if(json_last_error()) {
            return false;
        }

        // Combine the name and the Entities;

        $entities = array_map(function ($value) {
            return "&#{$value};";
        }, array_column($content, 'entity'));

        $XMLEntities = array_combine(array_keys($content), $entities);

        // Replace HTML named entites in the string;

        return strtr($string, $XMLEntities);

    }

    /**
     * Convert namespaced nodes into regular HTML nodes
     *
     * This method is responsible for transforming XML nodes into HTML nodes
     * Such as converting `<x2:a href=''>` into `<a href=''>`
     *
     * The method alos convert block elements such as `<x2:div/>` or `<x2:p/>`
     * into `<table/>` or `<td/>` elements
     *
     * @param object $element
     *
     * @return null
     * @ignore
     */
    protected function transformNode($element)
    {

        /*

            This is where we convert namespace node into regular node such as

            <x2:a href=''> into <a href=''>

            This is also where we convert <x2:div /> or <x2:p />

            into <table /> or <td />

        */

        if(!$element) {
            return;
        }


        /**
         * Now let's search for the children of the elements that should be transformed
         * However, we cannot user a foreach loop to accomplish this.
         *
         * ##### Why?
         *
         * Because we are going to be replacing child nodes with a lot of `<table/>`, `<tr/>` and other none namespaced HTML element.
         *
         * Since nodes are passed by reference, then, when we remove an element by replacing it with another one, the element will no longer exist in the nodelist and the next one in line takes its position of index.
         *
         * Hence, when foreach reaches the next iteration, it will skip the index and the node will not be processed.
         *
         * We definitely don't wanna skip any node!
         *
         * Solution: Use `for` loop instead. ;)
         */
        for($x = 0; $x < $element->childNodes->length; $x++) {

            // Get the childNode;

            $node = $element->childNodes->item($x);

            /**
             * Unfortunately, there are different kinds of node!
             * But we want only element Nodes
            */

            if(!$this->isElement($node)) {
                continue;
            }

            /**
             * Now let's get the original tagName;
             * converting namespaced nodes into HTML nodes &mdash; `x2:div` into `div`
            */

            $tag = $this->HTMLTag($node->nodeName);

            /**
             * If the tag is a block element such as DIV | P
             * We convert it into a table.
             * Otherwise, we replace it with an equivalent nodename that doesn't have namespace
            */

            if(in_array($tag, $this->block)) {
                $node = $this->convert2Tr($node);
            } else {
                $node = $this->renameNode($node, $tag);
            }

            /**
             * If the node has child Elements!
             * Then that means we're not done yet.
             * We just have to repeat the process again
            */

            if($node->childNodes->length) {
                $this->transformNode($node);
            }

        };

    }

    /**
     * Get the HTML Tag name without namespace
     *
     * @param string $nodeName
     *
     * @return string
     * @ignore
     */
    protected function HTMLTag($nodeName)
    {
        // This is achieved by simply removing the namespace from the tagName
        return str_replace($this->{"namespace"} . ":", '', $nodeName);
    }

    /**
     * @ignore
     */
    protected function groupTr($element)
    {

        // We'll search for all the `<tr/>` element that has no `<table/>` parent

        $xpath = (string)(new Translator("tr"));
        $nodes =  $this->xpath->query($xpath);

        for($x = 0; $x < $nodes->length; $x++) {

            $tr = $nodes->item($x); // Get the `<tr/>` element

            //Check if the parent is not a `<table/>` element

            if($tr->parentNode->nodeName != 'table') {

                // Get the parent Node;

                $parentNode = $tr->parentNode;

                // Create a Table Node So we can append the tr to it;

                $table = $this->dom->createElement('table');

                /*
                    Now we have to get a list of all <tr /> that should be appended to the table
                    Otherwise, a table will be forcefully and unwillingly created for each row irrespectively
                */

                $tr_list = array();
                foreach($tr->parentNode->childNodes as $childTr) {
                    $tr_list[] = $childTr ;
                }

                // Insert the table before the existence of the first table rows

                $parentNode->insertBefore($table, $tr_list[0]);

                /* Now! Append all the child table rows to it */

                foreach($tr_list as $childTr) {

                    /*
                        Let's handle some few error!
                        Shall We?
                    */

                    if($this->isEmpty($childTr)) {
                        continue;
                    }

                    /*
                        ME: Hey, only `<tr/>` elements can be a child of `<table/>`, am i right?

                        Firefox: Well, you didn't use me as your default browser so I won't tell you anything about `<tbody/>`

                        ME: :\ ?

                        Now back to business... I mean coding!
                    */

                    if($childTr->nodeName != 'tr') {
                        $tr = $this->dom->createElement('tr');
                        $td = $this->dom->createElement('td');
                        $tr->appendChild($td);
                        $td->appendChild($childTr);
                        $childTr = $tr;
                    };

                    $table->appendChild($childTr);

                }

                /*
                    Now we have to style and add the necessary attributes that most Email Client will require
                */

                $this->styleTable($table);

            };

        }

    }

    /**
     * Converts a node into `<tr/>` element
     *
     * @param object $node
     *
     * @return DOMNode
     * @ignore
     */
    protected function convert2Tr($node)
    {

        // Create Table Element;

        $tr = $this->dom->createElement('tr');
        $td = $this->renameNode($node, 'td');
        $td->parentNode->insertBefore($tr, $td);
        $tr->appendChild($td);
        $this->styleTd($td, $node);

        return $tr;

    }

    /**
     * @ignore
     */
    protected function styleTable($table)
    {

        /**
         * This table attribute below were recommended by MailMunch
         * You have a better one? Let me know now!
        */

        $attributes = array(
            "width" => '100%',
            "align" => 'left',
            "border" => 0,
            "cellspacing" => 0,
            "cellpadding" => 0,
            "style" => "max-width: 100%; table-layout: fixed; word-break: break-word;"
        );

        foreach($attributes as $name => $value) {
            $table->setAttribute($name, $value);
        }

    }

    /**
     * @ignore
     */
    protected function styleTd($td, $node)
    {

        /**
         * Right here, we pass the style of the element to the `<td/>`
         *
         * What i mean is if an element is created as `<x2:p style='width:100%' data-info/>`
         *
         * The attributes `style` and `data-info` will be forwareded to the `<td>` element that was created as a replacement for the `<x2:p/>` element
         *
         * So it becomes: `<td style='width:100%' data-info>`
        */

        if(!$this->isElement($node)) {
            return;
        }

        $attributes = array();

        foreach($attributes as $name => $value) {
            $td->setAttribute($name, $value);
        };

        foreach($node->attributes as $attr) {
            if($attr->name == 'style') {
                if(!in_array($this->HTMLTag($node->nodeName), $this->block)) {
                    continue;
                }
            } elseif(in_array($attr->name, ['href', 'src'])) {
                continue;
            }
            $td->setAttribute($attr->name, $attr->value);
        }

        $this->setMarker($td, $node);

    }

    /**
     * Check if a node contains no value
     *
     * @param object $node
     *
     * @return boolean
     * @ignore
     */
    protected function isEmpty($node)
    {
        if($node->nodeType == 3) {
            $nodeValue = trim($node->nodeValue);
            return empty($nodeValue);
        };
        return false;
    }

    /**
     * @ignore
     */
    protected function isElement($node)
    {
        return $node->nodeType === 1;
    }

    /**
     * @ignore
     */
    protected function setMarker($el, $node)
    {

        /**
         * Seriously, writing this script was so confusing!
         * Then the marker came to the rescue
         *
         * Marker helps us leave a trace that we can use to know which element was converted or associated to a `<table/>` or `<td/>` element
         *
         * It also enables us to identity the element's parent after the conversion
         */

        if(!$this->isElement($node)) {
            return;
        }

        $el->setAttribute("data-marker", $this->HTMLTag($node->nodeName));

        $markers = array(
            'id' => '#',
            'class' => '.'
        );

        foreach($markers as $attr => $selector) {
            $value = trim($node->getAttribute($attr));
            if(!empty($value)) {
                $marker = $el->getAttribute("data-marker");
                if($attr == 'id') {
                    $marker .= "{$selector}{$value}";
                } else {
                    $marked = implode('.', array_map('trim', explode(" ", $value)));
                    $marker .= ".{$marked}";
                };
                $el->setAttribute("data-marker", $marker);
            };
        };

    }

    /**
     * @ignore
     */
    protected function renameNode($node, $tag)
    {

        /**
         * DOMDocument Node cannot be renamed.
         * Therefore, we need to create a new element, assign the new name to it and replace the old element
         *
         * If you ask me one more time why we use `for` loop instead of a `foreach` to transform Nodes,
         * I'll punch your face!
         *
        */

        $newNode = $this->dom->createElement($tag);

        // preserve attributes;
        foreach($node->attributes as $attr) {
            $newNode->setAttribute($attr->name, $attr->value);
        }

        // preserve children
        foreach($node->childNodes as $childNode) {
            $newNode->appendChild($childNode->cloneNode(true));
        }

        $node->parentNode->replaceChild($newNode, $node);

        return $newNode;

    }

    /**
     * Render the converted HTML syntax as an email-compatible table structure.
     *
     * This method processes the provided HTML syntax and converts it into an email-compatible
     * table structure. It applies necessary transformations, such as converting internal CSS into
     * inline CSS and adding required styles and attributes to ensure compatibility across email clients.
     *
     * @param string|null $css_selector (Optional) A CSS selector to filter and render only the selected part of the HTML syntax.
     *                                  If provided, only the elements matching the selector will be included in the rendered output.
     *                                  Default: `null` (render the entire HTML syntax)
     *
     * @return string The rendered email-compatible table structure as a string.
     */
    public function render(?string $css_selector = null)
    {

        if(empty($this->errors)) {

            // get the root element;

            $element = $this->dom->getElementsByTagNameNS($this->domain, $this->hashTag)->item(0);

            /**
             * Now! Convert Internal CSS into Inline CSS
             * Unless you want email clients to stripe your `<style/>` tag and make your email look like shit!
            */

            $this->captureCss($element);

            /*
                This is the first instance where we call the transformNode;
                Inside it, a recursion occurs until all nodes are completely transformed
            */

            $this->transformNode($element);

            /**
             * Oh No! Now there are bunch of `<tr><td/></tr>` everywhere
             * None wrapped within a table.
             * Now! Let's group each `tr` based on parent Element
            */

            $this->groupTr($element);

            // Let's get the final result!

            $result = '';

            $nodeParent = $this->renderMode($element, $css_selector);

            if($nodeParent === $element) {

                foreach($nodeParent->childNodes as $node) {
                    $result .= $this->dom->saveXML($node) . "\n";
                }

            } else {
                $result = $this->dom->saveXML($nodeParent);
            }

            /**
             * Hurray!
             * Finally, We made it!
             *
             * Someone should consider buying me an ice cream
            */

            return $result;

        } else {
            return false;
        }

    }

    /**
     * Converts css selector into XPath and uses it to search for element to render
     *
     * @param object $element
     * @param string $css_selector
     *
     * @return DOMNode
     * @ignore
     */
    protected function renderMode($element, $css_selector)
    {

        if(!empty($css_selector)) {

            $css_selector = preg_replace("/{$this->namespace}:/i", '', trim($css_selector));

            $xquery = (string)(new Translator($css_selector));

            $node = $this->xpath->query($xquery)->item(0);

            if($node) {
                $element = $node;
            }

        };

        return $element;

    }

    /**
     * Get internal css rules and convert them into an array
     *
     * @param object $node
     *
     * @return array
     * @ignore
     */
    protected function captureCss($node)
    {

        $rules = array();

        /*

            I'd like you to know that your style tag must also start with the x2: namespace

            <x2:style> Your style here </x2:style>

        */

        // get all available style tags;

        $styles = $this->xpath->query(".//{$this->namespace}:style", $node);

        // Make them inline;

        foreach($styles as $style) {
            $style->nodeValue = str_replace("{$this->namespace}:", '', $style->nodeValue);
            $this->parse_css($style->nodeValue, $rules);
        };

        // return the rules as an array;

        return $rules;

    }

    /**
     * This is literally the method the parses the css rules and turns them into an array
     *
     * @param string $css
     * @param mixed $css_array
     *
     * @return [type]
     * @ignore
     */
    protected function parse_css(string $css, &$css_array)
    {

        $elements = explode('}', $css);

        foreach ($elements as $element) {

            $rule_break = array_filter(array_map('trim', explode('{', $element)));

            if(count($rule_break) < 2) {
                continue;
            }

            // get the name of the CSS element

            $name = trim($rule_break[0]);
            $name = preg_replace("/\s+/", ' ', $name);
            $name = preg_replace("/{$this->namespace}:/i", '', $name);

            if(substr($name, 0, 1) == '@') {
                continue;
            }

            $xPath = (string)(new Translator($name));
            $xPath = preg_replace("/\/\/(\w+)/i", "//{$this->namespace}:$1", $xPath);

            // get all the key:value pair styles
            $rules = array_filter(array_map('trim', explode(';', $rule_break[1])));

            $container = array();

            // remove element name from first property element
            foreach($rules as $rule) {
                $style_break = array_map('trim', explode(":", $rule));
                $container[ $style_break[0] ] = $style_break[1];
            };

            if(array_key_exists($name, $css_array)) {
                $css_array[ $name ] = array_merge($css_array[ $name ], $container);
            } $css_array[ $name ] = $container;

            // convert the internal css into inline css;

            $this->injectInlineCss($this->xpath->query($xPath), $container);

        }

        return $css_array;

    }

    /**
     * @ignore
     */
    protected function injectInlineCss($nodes, $style)
    {

        if(!$nodes) {
            return;
        }

        /*
            Convert the style from an array to a string
        */

        $inlineStyle = [];

        foreach($style as $key => $value) {
            $inlineStyle[] = "{$key}: $value";
        };

        $inlineStyle = implode("; ", $inlineStyle);

        // Now push the string into the node;

        foreach(iterator_to_array($nodes) as $node) {
            $node->setAttribute('style', $inlineStyle);
        }

    }

}
