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
 * <h1> This is the first Instant Article</h1>
 *  or
 * <h1> This is the <b>first</b> Instant Article</h1>
 */
class H1 extends TextContainer
{
    /**
     * @var string text align. Values: "op-left"|"op-center"|"op-right"
     */
    private string $textAlignment = "";

    /**
     * @var string text position. Values: "op-vertical-below"|"op-vertical-above"|"op-vertical-center"
     */
    private string $position = "";

    private function __construct()
    {
    }

    /**
     * @return H1
     */
    public static function create()
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
    public function withTextAlignment(string $text_alignment): H1
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
     * @deprecated
     *
     * @param string $position
     * @return $this
     */
    public function withPostion(string $position): H1
    {
        return $this->withPosition($position);
    }

    /**
     * The Text position that will be used.
     *
     * @see Caption::POSITION_ABOVE
     * @see Caption::POSITION_BELOW
     * @see Caption::POSITION_CENTER
     *
     * @param string $position that will be used.
     *
     * @return $this
     */
    public function withPosition(string $position): H1
    {
        Type::enforceWithin(
            $position,
            Vector {
                Caption::POSITION_ABOVE,
                Caption::POSITION_BELOW,
                Caption::POSITION_CENTER
            }
        );
        $this->position = $position;

        return $this;
    }

    /**
     * Structure and create the H1 in a DOMNode.
     *
     * @param \DOMDocument $document - The document where this element will be appended.
     *
     * @return \DOMNode
     */
    public function toDOMElement(\DOMDocument $document): \DOMNode
    {
        if (!$this->isValid()) {
            return $this->emptyElement($document);
        }

        $h1 = $document->createElement('h1');

        $classes = Vector {};
        if ($this->position) {
            $classes->add($this->position);
        }
        if ($this->textAlignment) {
            $classes->add($this->textAlignment);
        }
        if (!empty($classes)) {
            $h1->setAttribute('class', implode(' ', $classes));
        }

        $h1->appendChild($this->textToDOMDocumentFragment($document));

        return $h1;
    }
}
