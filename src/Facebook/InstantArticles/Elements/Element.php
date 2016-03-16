<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

/**
 * Class Element
 * This class is the meta each tag element that contains rendering code for the
 * tags
 *
 */
abstract class Element
{
    abstract public function toDOMElement();

    /**
     * Renders the Element content
     * @param string $doctype the doctype will be applied to document. I.e.: '<!doctype html>'
     * @return string with the content rendered
     */
    public function render($doctype = '', $formated = false)
    {
        $document = new \DOMDocument();
        $document->preserveWhiteSpace = !$formated;
        $document->formatOutput = $formated;
        $element = $this->toDOMElement($document);
        $document->appendChild($element);
        return $doctype.$document->saveXML($element);
    }

    /**
     * Appends unescaped HTML to a element using the right strategy.
     * @param DOMElement $element - The element to append the HTML to
     * @param string $content - The unescaped HTML to append
     */
    protected function dangerouslyAppendUnescapedHTML($element, $content)
    {
        $raw_node = $element->ownerDocument->createDocumentFragment();
        $raw_node->appendXML($content);
        $element->appendChild($raw_node);
    }
}
