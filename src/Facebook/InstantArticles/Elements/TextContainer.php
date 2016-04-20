<?php
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
 * Base class for components accepting formatted text. It can contain bold, italic and links.
 *
 * Example:
 * This is a <b>formatted</b> <i>text</i> for <a href="https://foo.com">your article</a>.
 *
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/body-text}
 */
abstract class TextContainer extends Element
{
    /**
     * @var array The content is a list of strings and FormattingElements
     */
    private $textChildren = array();

    /**
     * Adds content to the formatted text.
     *
     * @param string|FormattedText The content can be a string or a FormattedText.
     */
    public function appendText($child)
    {
        Type::enforce($child, array(Type::STRING, FormattedText::getClassName(), TextContainer::getClassName()));
        $this->textChildren[] = $child;

        return $this;
    }

    /**
     * @return array<string|FormattedText> All text token for this text container.
     */
    public function getTextChildren()
    {
        return $this->textChildren;
    }

    /**
     * Structure and create the full text in a DOMDocumentFragment.
     *
     * @param DOMDocument $document - The document where this element will be appended (optional).
     */
    public function textToDOMDocumentFragment($document = null)
    {
        if (!$document) {
            $document = new \DOMDocument();
        }

        $fragment = $document->createDocumentFragment();

        // Generate markup
        foreach ($this->textChildren as $content) {
            if (Type::is($content, Type::STRING)) {
                $text = $document->createTextNode($content);
                $fragment->appendChild($text);
            } else {
                $fragment->appendChild($content->toDOMElement($document));
            }
        }

        if (!$fragment->hasChildNodes()) {
            $fragment->appendChild($document->createTextNode(''));
        }

        return $fragment;
    }

    /**
     * Overrides the @see Element::isValid().
     *
     * @return true for valid tag, false otherwise.
     */
    public function isValid()
    {
        $textContent = '';

        foreach ($this->textChildren as $content) {
            // Recursive check on TextContainer, if something inside is valid, this is valid.
            if (Type::is($content, TextContainer::getClassName()) && $content->isValid()) {
                return true;
            // If is string content, concat to check if it is not only a bunch of empty chars.
            } else if (Type::is($content, Type::STRING)) {
                $textContent = $textContent.$content;
            }
        }

        // Stripes empty spaces, &nbsp;, <br/>, new lines
        $textContent = strip_tags($textContent);
        $textContent = preg_replace("/[\r\n\s]+/", "", $textContent);
        $textContent = str_replace("&nbsp;", '', $textContent);

        return !empty($textContent);
    }
}
