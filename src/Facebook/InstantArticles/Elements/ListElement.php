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
class ListElement extends Element implements ChildrenContainer
{
    /**
     * @var boolean Checks if it is ordered or unordered
     */
    private bool $isOrdered = false;

    /**
     * @var ListItem[] Items of the list
     */
    private Vector<ListItem> $items = Vector {};

    protected function __construct()
    {
    }

    /**
     * Factory method for an Ordered list
     *
     * @return ListElement the new instance List as an ordered list
     */
    public static function createOrdered(): ListElement
    {
        $list = new self();
        $list->enableOrdered();

        return $list;
    }

    /**
     * Factory method for an unordered list
     *
     * @return ListElement the new instance List as an unordered list
     */
    public static function createUnordered(): ListElement
    {
        $list = new self();
        $list->disableOrdered();

        return $list;
    }

    /**
     * Adds a new item to the List
     *
     * @param ListItem $new_item The new item that will be pushed to the end of the list
     *
     * @return $this
     */
    public function addItem(ListItem $new_item): this
    {
        $this->items->add($new_item);

        return $this;
    }

    /**
     * Sets all items of the list as the array on the parameter
     *
     * @param Vector<ListItem> $new_items The new items. Replaces all items from the list
     *
     * @return $this
     */
    public function withItems(Vector<ListItem> $new_items): this
    {
        $this->items = Vector {};
        $this->items->addAll($new_items);
        return $this;
    }

    /**
     * Makes the list become ordered
     *
     * @return $this
     */
    public function enableOrdered(): this
    {
        $this->isOrdered = true;

        return $this;
    }

    /**
     * Makes the list become unordered
     *
     * @return $this
     */
    public function disableOrdered(): this
    {
        $this->isOrdered = false;

        return $this;
    }

    /**
     * @return Vector<ListItem> the list text items
     */
    public function getItems(): Vector<ListItem>
    {
        return $this->items;
    }

    /**
     * @return boolean if the list is ordered
     */
    public function isOrdered(): bool
    {
        return $this->isOrdered;
    }

    /**
     * Structure and create the full Video in a XML format DOMNode.
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

        if ($this->isOrdered) {
            $element = $document->createElement('ol');
        } else {
            $element = $document->createElement('ul');
        }

        if ($this->items) {
            foreach ($this->items as $item) {
                if ($item) {
                    $element->appendChild($item->toDOMElement($document));
                }
            }
        }

        return $element;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid ListElement that contains at least one ListItem's valid, false otherwise.
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
     * @return Vector<Element> contained by List.
     */
    public function getContainerChildren(): Vector<Element>
    {
        $children = Vector {};
        if ($this->items) {
            foreach ($this->items as $item) {
                $children->add($item);
            }
        }
        return $children;
    }
}
