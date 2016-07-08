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
 * Class Sponsor that represents branded content.
 *
 * Example:
 * <ul class="op-sponsors">
 *     <li><a href="http://facebook.com/your-sponsor" rel="facebook"></a></li>
 * </ul>
 *
 */
class Sponsor extends ListElement
{
    /**
     * @var string page URL.
     */
    private $page_url;

    /**
     * Factory method for a Sponsor.
     *
     * @return Sponsor the new instance.
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Sets the page url sponsor. Overrides the previous set URL.
     *
     * @param string The page url that will be the sponsor.
     *
     * @return $this
     */
    public function withPageUrl($url)
    {
        $this->withItems([]);
        $this->addItem(
            ListItem::create()
                ->appendText(
                    Anchor::create()
                        ->withHref($url)
                        ->withRel("facebook")
                )
        );
        return $this;
    }

    /**
     * Structure and create the full Video in a XML format DOMElement.
     *
     * @param \DOMDocument $document where this element will be appended. Optional
     *
     * @return \DOMElement
     */
    public function toDOMElement($document = null)
    {
        $element = parent::toDOMElement($document);
        if ($this->isValid()) {
            $element->setAttribute('class', 'op-sponsors');
        }

        return $element;
    }
}
