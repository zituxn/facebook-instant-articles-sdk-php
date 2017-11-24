<?hh
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

use Facebook\InstantArticles\Validators\Type;

/**
 * Base class for elements that may contain embedded HTML
 */
abstract class ElementWithHTML extends Element
{
    /**
     * @var \DOMNode The HTML of the content.
     */
    protected ?\DOMNode $html;

    /**
     * @var string The HTML of the content (as string).
     */
    protected ?string $html_string;

    /**
     * Sets the unescaped HTML.
     *
     * @param \DOMNode|string $html The unescaped HTML.
     *
     * @return $this
     */
    public function withHTML(\DOMNode $html): this
    {
        // If this is raw HTML source, wrap in a CDATA section as it could contain JS etc. with characters (such as &) that are not allowed in unescaped form
        // if (Type::is($html, Type::STRING)) {
        //     $html = new \DOMCdataSection($html);
        // }
        $this->html = $html;
        return $this;
    }

    /**
     * Sets the HTML String.
     *
     * @param string $html The HTML string.
     *
     * @return $this
     */
    public function withHTMLString(string $html_string): this
    {
        $this->html_string = $html_string;
        return $this;
    }

    /**
     * Gets the unescaped HTML.
     *
     * @return \DOMNode The unescaped HTML.
     */
    public function getHtml(): ?\DOMNode
    {
        return $this->html;
    }

    /**
     * Appends unescaped HTML to a element using the right strategy.
     *
     * @param \DOMNode $element - The element to append the HTML to.
     * @param \DOMNode $content - The unescaped HTML to append.
     */
    protected function dangerouslyAppendUnescapedHTML(\DOMNode $element, \DOMNode $content): void
    {
        $imported = $element->ownerDocument->importNode($content, true);
        $element->appendChild($imported);
    }

    /**
     * Appends unescaped HTML String to a element using the right strategy.
     *
     * @param \DOMNode $element - The element to append the HTML to.
     * @param \DOMNode $content - The unescaped HTML to append.
     */
    protected function dangerouslyAppendUnescapedHTMLString(\DOMNode $element, string $html_string): void
    {
        $imported = $element->ownerDocument->createCDATASection($html_string);
        $element->appendChild($imported);
    }

}
