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
 * Class RelatedItem to represent each of the RelatedArticles.
 * @see RelatedArticles
 */
class RelatedItem extends Element
{
    /**
     * @var string The related Article URL
     */
    private string $url = "";

    /**
     * @var boolean If the article is sponsored
     */
    private bool $sponsored = false;

    private function __construct()
    {
    }

    /**
     * Factory method for the RelatedItem
     *
     * @return RelatedItem
     */
    public static function create(): RelatedItem
    {
        return new self();
    }

    /**
     * Sets the article URL
     *
     * @param string $url The related article URL
     *
     * @return $this
     */
    public function withURL(string $url): this
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Makes this item to be an sponsored one
     *
     * @return $this
     */
    public function enableSponsored(): this
    {
        $this->sponsored = true;

        return $this;
    }

    /**
     * Makes this item to *NOT* be an sponsored one
     *
     * @return $this
     */
    public function disableSponsored(): this
    {
        $this->sponsored = false;

        return $this;
    }

    /**
     * @return string The RelatedItem url
     */
    public function getURL(): string
    {
        return $this->url;
    }

    /**
     * @return boolean true if it is sponsored, false otherwise.
     */
    public function isSponsored(): bool
    {
        return $this->sponsored;
    }

    /**
     * Structure and create the full ArticleVideo in a XML format DOMNode.
     *
     * @param \DOMDocument $document where this element will be appended. Optional
     *
     * @return \DOMNode
     */
    public function toDOMElement(\DOMDocument $document): \DOMNode
    {
        if (!$this->isValid()) {
            return $this->emptyElement($document);
        }

        $element = $document->createElement('li');
        if ($this->sponsored) {
            $element->setAttribute('data-sponsored', 'true');
        }
        $element->appendChild(
            Anchor::create()->withHref($this->url)->toDOMElement($document)
        );

        return $element;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid RelatedItem that contains valid url, false otherwise.
     */
    public function isValid(): bool
    {
        return !Type::isTextEmpty($this->url);
    }
}
