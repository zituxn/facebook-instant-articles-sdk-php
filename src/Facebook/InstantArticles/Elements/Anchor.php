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
 * An anchor.
 *
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/body-text}
 */
class Anchor extends FormattedText
{
    /**
     * @var string URL to link to.
     */
    private string $href = "";

    /**
     * @var string rel.
     */
    private string $rel = "";

    private function __construct()
    {
    }

    /**
     * @return Anchor
     */
    public static function create(): Anchor
    {
        return new self();
    }

    /**
     * @param string $href the anchor link
     *
     * @return $this
     */
    public function withHref(string $href): this
    {
        $this->href = $href;
        return $this;
    }

    /**
     * @param string $rel the anchor rel attribute
     *
     * @return $this
     */
    public function withRel(string $rel): this
    {
        $this->rel = $rel;
        return $this;
    }

    /**
     * @return string the anchor link
     */
    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * @return string the rel attribute
     */
    public function getRel(): string
    {
        return $this->rel;
    }

    /**
     * Structure and create <a> node.
     *
     * @param \DOMDocument $document - The document where this element will be appended (optional).
     *
     * @return \DOMNode
     */
    public function toDOMElement(\DOMDocument $document): \DOMNode
    {
        $anchor = $document->createElement('a');

        if (!Type::isTextEmpty($this->href)) {
            $anchor->setAttribute('href', $this->href);
        }
        if (!Type::isTextEmpty($this->rel)) {
            $anchor->setAttribute('rel', $this->rel);
        }

        $anchor->appendChild($this->textToDOMDocumentFragment($document));

        return $anchor;
    }

    /**
    * Overrides the TextContainer::isValid().
    * @see TextContainer::isValid().
     * @return true for valid Anchor when it has href, false otherwise.
     */
    public function isValid(): bool
    {
        return !Type::isTextEmpty($this->href) && parent::isValid();
    }
}
