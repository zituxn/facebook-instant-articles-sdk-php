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
 * Each paragraph of article should be an instance of this class.
 *
 * Example:
 * <aside> This is the pullquote </p>
 *
 * or
 *
 * <aside>
 *    Long life, pull quote will have.
 *    <cite>Unknown Jedi</cite>
 * </aside>
 *
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/body-text}
 */
class Pullquote extends TextContainer
{
    /**
     * @var Cite Content that will be shown on <cite>...</cite> tags. Optional.
     */
    private ?Cite $attribution;

    private function __construct()
    {
    }

    /**
     * @return Pullquote
     */
    public static function create(): Pullquote
    {
        return new self();
    }

    /**
     * Sets the attribution string
     *
     * @param Cite $attribution The attribution text
     *
     * @return $this
     */
    public function withAttribution(Cite $attribution): this
    {
        $this->attribution = $attribution;
        return $this;
    }

    /**
     * Sets the attribution string
     *
     * @param string $attribution The attribution text
     *
     * @return $this
     */
    public function withAttributionString(string $attribution): this
    {
        $cite =  Cite::create();
        $cite->appendText($attribution);
        $this->attribution = $cite;
        return $this;
    }


    /**
     * @return Cite The attribution
     */
    public function getAttribution(): ?Cite
    {
        return $this->attribution;
    }

    /**
     * Structure and create the full Pullquote in a DOMNode.
     *
     * @param \DOMDocument $document - The document where this element will be appended (optional).
     *
     * @return \DOMNode
     */
    public function toDOMElement(\DOMDocument $document): \DOMNode
    {
        if (!$this->isValid()) {
            return $this->emptyElement($document);
        }

        $element = $document->createElement('aside');

        $element->appendChild($this->textToDOMDocumentFragment($document));

        // Attribution Citation
        if ($this->attribution !== null) {
            $element->appendChild($this->attribution->toDOMElement($document));
        }

        return $element;
    }
}
