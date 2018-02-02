<?hh // strict
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
abstract class TextContainer extends Element implements ChildrenContainer
{
    /**
     * @var vec The content is a list of TextContainer
     */
    private vec<mixed> $textChildren = vec[];

    /**
     * Adds content to the formatted text.
     *
     * @param string|TextContainer The content can be a string or a FormattedText.
     *
     * @return $this
     */
    public function appendText(mixed $child): this
    {
        // TODO Make sure this is string|TextContainer
        $this->textChildren[] = $child;
        return $this;
    }

    /**
     * Clears the text.
     */
    public function clearText(): this
    {
        $this->textChildren = vec[];
        return $this;
    }

    /**
     * @return vec<string|TextContainer> All text token for this text container.
     */
    public function getTextChildren(): vec<mixed>
    {
        return $this->textChildren;
    }

    /**
     * Structure and create the full text in a DOMDocumentFragment.
     *
     * @param \DOMDocument $document - The document where this element will be appended (optional).
     *
     * @return \DOMNode
     */
    public function textToDOMDocumentFragment(\DOMDocument $document): \DOMNode
    {
        $fragment = $document->createDocumentFragment();

        // Generate markup
        foreach ($this->textChildren as $content) {
            if (is_string($content)) {
                $text = $document->createTextNode($content);
                $fragment->appendChild($text);
            } elseif ($content instanceof TextContainer) {
                Element::appendChild($fragment, $content, $document);
            }
        }

        if (!$fragment->hasChildNodes()) {
            $fragment->appendChild($document->createTextNode(''));
        }

        return $fragment;
    }

    /**
     * Build up a string with the content from children text container
     *
     * @return string the unformated plain text content from children
     */
    public function getPlainText(): string
    {
        $text = '';

        // Generate markup
        foreach ($this->textChildren as $content) {
            if (is_string($content)) {
                $text .= $content;
            } elseif ($content instanceof TextContainer) {
                $text .= $content->getPlainText();
            }
        }

        return $text;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid tag, false otherwise.
     */
    public function isValid(): bool
    {
        $textContent = "";

        foreach ($this->textChildren as $content) {
            // Recursive check on TextContainer, if something inside is valid, this is valid.
            if ($content instanceof TextContainer) {
                return $content->isValid();
            // If is string content, concat to check if it is not only a bunch of empty chars.
            } elseif (is_string($content)) {
                $textContent = $textContent.$content;
            }
        }
        return !Type::isTextEmpty($textContent);
    }

    /**
     * Implements the ChildrenContainer::getContainerChildren().
     *
     * @see ChildrenContainer::getContainerChildren().
     * @return vec of TextContainer
     */
    public function getContainerChildren(): vec<Element>
    {
        $children = vec[];

        foreach ($this->textChildren as $content) {
            if ($content instanceof TextContainer) {
                $children[] = $content;
            }
        }

        return $children;
    }
}
