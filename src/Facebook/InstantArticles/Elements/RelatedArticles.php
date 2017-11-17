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
 * Class List that represents a simple HTML list
 *
 * Example Unordered:
 * <ul>
 *     <li>Dog</li>
 *     <li>Cat</li>
 *     <li>Fox</li>
 * </ul>
 *
 * Example Ordered:
 * <ol>
 *     <li>Groceries</li>
 *     <li>School</li>
 *     <li>Sleep</li>
 * </ol>
 */
class RelatedArticles extends Element implements \Facebook\InstantArticles\Elements\ChildrenContainer
{
    /**
     * @var RelatedItem[] The related Articles
     */
    private Vector<RelatedItem> $items = Vector {};

    /**
     * @var string The title of the Related Articles content
     */
    private string $title = "";

    private function __construct()
    {
    }

    /**
     * Factory method for the RelatedArticles list
     *
     * @return RelatedArticles the new instance of RelatedArticles
     */
    public static function create(): RelatedArticles
    {
        return new self();
    }

    /**
     * Adds a new related article item
     *
     * @param RelatedItem $item The related article item
     *
     * @return $this
     */
    public function addRelated(RelatedItem $item): this
    {
        $this->items->add($item);

        return $this;
    }

    /**
     * Sets the title of Related articles content block
     *
     * @param string $title the name of related articles block
     *
     * @return $this
     */
    public function withTitle(string $title): this
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return RelatedItem[] The RelatedItem's
     */
    public function getItems(): Vector<RelatedItem>
    {
        return $this->items;
    }

    /**
     * @return string the name of related articles block
     */
    public function getTitle(): string
    {
        return $this->title;
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

        $element = $document->createElement('ul');
        $element->setAttribute('class', 'op-related-articles');
        if ($this->title) {
            $element->setAttribute('title', $this->title);
        }

        if ($this->items) {
            foreach ($this->items as $item) {
                $element->appendChild($item->toDOMElement($document));
            }
        }

        return $element;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid RelatedArticles that contains at least one RelatedItem's valid, false otherwise.
     */
    public function isValid(): bool
    {
        foreach ($this->items as $item) {
            if ($item->isValid()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Implements the ChildrenContainer::getContainerChildren().
     *
     * @see ChildrenContainer::getContainerChildren().
     * @return array of Elements contained by Image.
     */
    public function getContainerChildren(): Vector<Element>
    {
        $children = Vector {};

        foreach ($this->items as $item) {
            $children->add($item);
        }

        return $children;
    }
}
