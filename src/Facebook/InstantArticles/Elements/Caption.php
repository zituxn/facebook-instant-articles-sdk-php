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
 * A caption for any element.
 * A caption can be included in any of the items:
 * <ul>
 *     <li>Image</li>
 *     <li>Video</li>
 *     <li>Slideshow</li>
 *     <li>Map</li>
 *     <li>Interactive</li>
 * </ul>.
 *
 * Example:
 *    <figcaption class="op-vertical-below">
 *        <h1>Caption Title</h1>
 *        <h2>Caption SubTitle</h2>
 *    </figcaption>
 *
 * @see Image
 * @see Video
 * @see Slideshow
 * @see Map
 * @see Interactive
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/caption}
 */
class Caption extends FormattedText
{
    // Font size
    const SIZE_SMALL = 'op-small';
    const SIZE_MEDIUM = 'op-medium';
    const SIZE_LARGE = 'op-large';
    const SIZE_XLARGE = 'op-extra-large';

    // Text alignment (horizontal)
    const ALIGN_LEFT = 'op-left';
    const ALIGN_CENTER = 'op-center';
    const ALIGN_RIGHT = 'op-right';

    // Vertical position of the block
    const POSITION_BELOW = 'op-vertical-below';
    const POSITION_ABOVE = 'op-vertical-above';
    const POSITION_CENTER = 'op-vertical-center';

    // Vertical alignment of the block
    const VERTICAL_TOP = 'op-vertical-top';
    const VERTICAL_BOTTOM = 'op-vertical-bottom';
    const VERTICAL_CENTER = 'op-vertical-center';

    /**
     * @var H1 The caption title. REQUIRED
     */
    private ?H1 $title;

    /**
     * @var H2 The caption subtitle. optional
     */
    private ?H2 $subTitle;

    /**
     * @var Cite The credit text. optional
     */
    private ?Cite $credit;

    /**
     * @var string text Size. Values: "op-small"|"op-medium"|"op-large"|"op-extra-large"
     */
    private string $fontSize = "";

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
     * @return Caption
     */
    public static function create(): Caption
    {
        return new self();
    }

    /**
     * The caption title. REQUIRED.
     *
     * @param H1 $title the caption text that will be shown
     *
     * @return $this
     */
    public function withTitle(H1 $title): this
    {
        $this->title = $title;
        return $this;
    }

    /**
     * The caption sub title. optional.
     *
     * @param H2 $sub_title the caption sub title text that will be shown
     *
     * @return $this
     */
    public function withSubTitle(H2 $sub_title): this
    {
        $this->subTitle = $sub_title;
        return $this;
    }

    /**
     * The caption credit. optional.
     *
     * @param Cite $credit the caption credit text that will be shown
     *
     * @return $this
     */
    public function withCredit(Cite $credit): this
    {
        $this->credit = $credit;
        return $this;
    }

    /**
     * The Fontsize that will be used.
     *
     * @see Caption::SIZE_SMALL
     * @see Caption::SIZE_MEDIUM
     * @see Caption::SIZE_LARGE
     * @see Caption::SIZE_XLARGE
     *
     * @param string $font_size the caption font size that will be used.
     *
     * @return $this
     */
    public function withFontsize(string $font_size): this
    {
        Type::enforceWithin(
            $font_size,
            vec[
                Caption::SIZE_XLARGE,
                Caption::SIZE_LARGE,
                Caption::SIZE_MEDIUM,
                Caption::SIZE_SMALL,
            ]
        );
        $this->fontSize = $font_size;

        return $this;
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
    public function withTextAlignment(string $text_alignment): this
    {
        Type::enforceWithin(
            $text_alignment,
            vec[
                Caption::ALIGN_RIGHT,
                Caption::ALIGN_LEFT,
                Caption::ALIGN_CENTER,
            ]
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
    public function withVerticalAlignment(string $vertical_alignment): this
    {
        Type::enforceWithin(
            $vertical_alignment,
            vec[
                Caption::VERTICAL_TOP,
                Caption::VERTICAL_BOTTOM,
                Caption::VERTICAL_CENTER,
            ]
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
     *
     * @return $this
     */
    public function withPosition(string $position): this
    {
        Type::enforceWithin(
            $position,
            vec[
                Caption::POSITION_ABOVE,
                Caption::POSITION_BELOW,
                Caption::POSITION_CENTER,
            ]
        );
        $this->position = $position;

        return $this;
    }

    /**
     * @return H1 the caption text title
     */
    public function getTitle(): ?H1
    {
        return $this->title;
    }

    /**
     * @return H2 the caption text subtitle
     */
    public function getSubTitle(): ?H2
    {
        return $this->subTitle;
    }

    /**
     * @return Cite the credit text
     */
    public function getCredit(): ?Cite
    {
        return $this->credit;
    }

    /**
     * @return string the Font size.
     *
     * @see Caption::SIZE_SMALL
     * @see Caption::SIZE_MEDIUM
     * @see Caption::SIZE_LARGE
     * @see Caption::SIZE_XLARGE
     */
    public function getFontSize(): string
    {
        return $this->fontSize;
    }

    /**
     * @return string the Font size.
     *
     * @see Caption::ALIGN_RIGHT
     * @see Caption::ALIGN_LEFT
     * @see Caption::ALIGN_CENTER
     */
    public function getTextAlignment(): string
    {
        return $this->textAlignment;
    }

    /**
     * @return string the Font size.
     *
     * @see Caption::POSITION_ABOVE
     * @see Caption::POSITION_BELOW
     * @see Caption::POSITION_CENTER
     */
    public function getPosition(): string
    {
        return $this->position;
    }

    /**
     * @return string the Vertical Alignment.
     *
     * @see Caption::VERTICAL_TOP
     * @see Caption::VERTICAL_BOTTOM
     * @see Caption::VERTICAL_CENTER
     */
    public function getVerticalAlignment(): string
    {
        return $this->verticalAlignment;
    }

    /**
     * Structure and create the full ArticleImage in a XML format DOMNode.
     *
     * @param \DOMDocument $document where this element will be appended. Optional
     * @return \DOMNode
     */
    public function toDOMElement(\DOMDocument $document): \DOMNode
    {
        $element = $document->createElement('figcaption');

        // title markup REQUIRED
        if ($this->title && (!$this->subTitle && !$this->credit)) {
             $element->appendChild($this->title->textToDOMDocumentFragment($document));
        } elseif ($this->title) {
            $element->appendChild($this->title->toDOMElement($document));
        } else {
            Element::appendChild($element, $this->title, $document);
         }

        // subtitle markup optional
        Element::appendChild($element, $this->subTitle, $document);

        $element->appendChild($this->textToDOMDocumentFragment($document));

        // credit markup optional
        Element::appendChild($element, $this->credit, $document);

        // Formating markup
        if ($this->textAlignment || $this->verticalAlignment || $this->fontSize || $this->position) {
            $classes = vec[];
            if ($this->textAlignment) {
                $classes[] = $this->textAlignment;
            }
            if ($this->verticalAlignment) {
                $classes[] = $this->verticalAlignment;
            }
            if ($this->fontSize) {
                $classes[] = $this->fontSize;
            }
            if ($this->position) {
                $classes[] = $this->position;
            }
            $element->setAttribute('class', implode(' ', $classes));
        }

        return $element;
    }

    /**
     * Overrides the TextContainer::isValid().
     *
     * @see TextContainer::isValid().
     * @return true for valid Caption when it is filled, false otherwise.
     */
    public function isValid(): bool
    {
        return parent::isValid() || ($this->title && $this->title->isValid());
    }
}
