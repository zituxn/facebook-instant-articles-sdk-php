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
 * An Interactive graphic.
 *
 * Example:
 * <figure class="op-interactive">
 *   <iframe class="no-margin" src="http://example.com/custom-interactive" height="60"></iframe>
 *   <figcaption>This graphic is awesome.</figcaption>
 * </figure>
 *
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/interactive}
 */
class Interactive extends ElementWithHTML implements ChildrenContainer, Captionable
{
    const NO_MARGIN = 'no-margin';
    const COLUMN_WIDTH = 'column-width';

    /**
     * @var Caption Descriptive text for your social embed.
     */
    private ?Caption $caption;

    /**
     * @var int The width of your interactive graphic.
     */
    private int $width = 0;

    /**
     * @var int The height of your interactive graphic.
     */
    private int $height = 0;

    /**
     * @var string The source of the content for your interactive graphic.
     */
    private string $source = "";

    /**
     * @var string The width setting for the interactive graphic.
     * @see Interactive::NO_MARGIN
     * @see Interactive::COLUMN_WIDTH
     */
    private string $margin = "";

    private function __construct()
    {
    }

    public static function create(): Interactive
    {
        return new self();
    }

    /**
     * Sets the caption for the social embed.
     *
     * @param Caption $caption - Descriptive text for your social embed.
     *
     * @return $this
     */
    public function withCaption(Caption $caption): this
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Sets the width of your interactive graphic.
     *
     * @param int $width The height of your interactive graphic.
     *
     * @return $this
     */
    public function withWidth(int $width): this
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Sets the height of your interactive graphic.
     *
     * @param int $height The height of your interactive graphic.
     *
     * @return $this
     */
    public function withHeight(int $height): this
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Sets the source for the interactive graphic.
     *
     * @param string $source The source of the content for your interactive graphic.
     *
     * @return $this
     */
    public function withSource(string $source): this
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Sets the margin setting of your interactive graphic.
     *
     * @param string $margin The margin setting of your interactive graphic.
     *
     * @see Interactive::NO_MARGIN
     * @see Interactive::COLUMN_WIDTH
     *
     * @return $this
     */
    public function withMargin(string $margin): this
    {
        Type::enforceWithin(
            $margin,
            vec[
                Interactive::NO_MARGIN,
                Interactive::COLUMN_WIDTH,
            ]
        );
        $this->margin = $margin;

        return $this;
    }

    /**
     * @return Caption the caption element
     */
    public function getCaption(): ?Caption
    {
        return $this->caption;
    }

    /**
     * @return int the width
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @return int the height
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @return string url source
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @return string the margin
     */
    public function getMargin(): string
    {
        return $this->margin;
    }

    /**
     * Structure and create the full Interactive in a DOMNode.
     *
     * @param \DOMDocument $document - The document where this element will be appended (optional).
     *
     * @return \DOMNode
     */
    public function toDOMElement(\DOMDocument $document): \DOMNode
    {
        $figure = $document->createElement('figure');
        $iframe = $document->createElement('iframe');

        $figure->appendChild($iframe);
        $figure->setAttribute('class', 'op-interactive');

        // Caption markup optional
        Element::appendChild($figure, $this->caption, $document);

        if ($this->source) {
            $iframe->setAttribute('src', $this->source);
        }

        if ($this->margin) {
            $iframe->setAttribute('class', $this->margin);
        }

        if ($this->width) {
            $iframe->setAttribute('width', strval($this->width));
        }

        if ($this->height) {
            $iframe->setAttribute('height', strval($this->height));
        }

        // Ad markup
        if ($this->html) {
            // Here we do not care about what is inside the iframe
            // because it'll be rendered in a sandboxed webview
            $this->dangerouslyAppendUnescapedHTML($iframe, $this->html);
        } else if ($this->html_string !== null) {
            $this->dangerouslyAppendUnescapedHTMLString($iframe, $this->html_string);
        } else {
            $iframe->appendChild($document->createTextNode(''));
        }

        return $figure;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid Interactive that contains valid source or html, false otherwise.
     */
    public function isValid(): bool
    {
        return $this->html || !Type::isTextEmpty($this->html_string) || (!Type::isTextEmpty($this->source) && $this->height && $this->width);
    }

    /**
     * Implements the ChildrenContainer::getContainerChildren().
     *
     * @see ChildrenContainer::getContainerChildren().
     * @return vec of Elements contained by Image.
     */
    public function getContainerChildren(): vec<Element>
    {
        $children = vec[];
        if ($this->caption) {
            $children[] = $this->caption;
        }
        return $children;
    }
}
