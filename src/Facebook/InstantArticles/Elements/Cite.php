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
 * Title for the Document
 *
 * Example:
 * <cite> This is the first Instant Article</cite>
 *  or
 * <cite> This is the <b>first</b> Instant Article</cite>
 */
class Cite extends TextContainer
{
    /**
     * @var string text align. Values: "op-left"|"op-center"|"op-right"
     */
    private string $textAlignment = "";

    /**
     * @var string vertical align. Values: "op-vertical-top"|"op-vertical-bottom"|"op-vertical-center"
     */
    private string $verticalAlignment = "";

    /**
     * @var string text position. Values: "op-vertical-below"|"op-vertical-above"|"op-vertical-center"
     */
    private string $position = "";

    private function __construct()
    {
    }

    /**
     * @return Cite
     */
    public static function create(): Cite
    {
        return new self();
    }

    /**
     * The Text alignment that will be used.
     *
     * @see Caption::ALIGN_RIGHT
     * @see Caption::ALIGN_LEFT
     * @see Caption::ALIGN_CENTER
     *
     * @param string $text_alignment alignment option that will be used.
     *
     * @return $this
     */
    public function withTextAlignment(string $text_alignment): Cite
    {
        Type::enforceWithin(
            $text_alignment,
            Vector {
                Caption::ALIGN_RIGHT,
                Caption::ALIGN_LEFT,
                Caption::ALIGN_CENTER,
            }
        );
        $this->textAlignment = $text_alignment;

        return $this;
    }

    /**
     * The vertical alignment that will be used.
     *
     * @see Caption::VERTICAL_TOP
     * @see Caption::VERTICAL_BOTTOM
     * @see Caption::VERTICAL_CENTER
     *
     * @param string $vertical_alignment alignment option that will be used.
     *
     * @return $this
     */
    public function withVerticalAlignment(string $vertical_alignment): Cite
    {
        Type::enforceWithin(
            $vertical_alignment,
            Vector {
                Caption::VERTICAL_TOP,
                Caption::VERTICAL_BOTTOM,
                Caption::VERTICAL_CENTER,
            }
        );
        $this->verticalAlignment = $vertical_alignment;

        return $this;
    }

    /**
     * The Text position that will be used.
     *
     * @see Caption::POSITION_ABOVE
     * @see Caption::POSITION_BELOW
     * @see Caption::POSITION_CENTER
     *
     * @param string $position that will be used.
     * @return $this
     */
    public function withPosition(string $position): Cite
    {
        Type::enforceWithin(
            $position,
            Vector {
                Caption::POSITION_ABOVE,
                Caption::POSITION_BELOW,
                Caption::POSITION_CENTER,
            }
        );
        $this->position = $position;

        return $this;
    }

    /**
     * Structure and create the <cite> in a DOMNode.
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

        $cite = $document->createElement('cite');

        $classes = Vector {};
        if ($this->position) {
            $classes->add($this->position);
        }
        if ($this->textAlignment) {
            $classes->add($this->textAlignment);
        }
        if ($this->verticalAlignment) {
            $classes->add($this->verticalAlignment);
        }
        if (!empty($classes)) {
            $cite->setAttribute('class', implode(' ', $classes));
        }
        $cite->appendChild($this->textToDOMDocumentFragment($document));

        return $cite;
    }
}
