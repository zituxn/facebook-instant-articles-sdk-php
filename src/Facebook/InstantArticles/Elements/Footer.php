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
 * Footer of the article.
 *
 * Example:
 * <body>
 *  <article>
 *    <footer>
 *      <aside>
 *        <p>The magazine thanks <a rel="facebook" href="...">The Rockefeller Foundation</a></p>
 *        <p>The magazine would also like to thank its readers.</p>
 *      </aside>
 *    </footer>
 *  </article>
 * </body>
 *
 * @see {link:https://developers.intern.facebook.com/docs/instant-articles/reference/footer}
 */
class Footer extends Element implements ChildrenContainer
{
    /**
     * @var string|Paragraph[] The text content of the credits
     */
    private vec<mixed> $credits = vec[];

    /**
     * @var Small Copyright information of the article
     */
    private ?Small $copyright;

    /**
     * @var RelatedArticles the related articles to be added to this footer. Optional
     */
    private ?RelatedArticles $relatedArticles;

    private function __construct()
    {
    }

    /**
     * @return Footer
     */
    public static function create(): Footer
    {
        return new self();
    }

    /**
     * Sets the text content of the credits
     *
     * @param vec<string|Paragraph> $credits - A list of paragraphs or a single string for the content of the credit.
     *
     * @return $this
     */
    public function withCredits(vec<mixed> $credits): this
    {
        $this->credits = $credits;

        return $this;
    }

    /**
     * Adds a new Paragraph to the credits
     *
     * @param Paragraph $credit - One Paragraph to be added as a credit.
     *
     * @return $this
     */
    public function addCredit(mixed $credit): this
    {
        $this->credits[] = $credit;

        return $this;
    }

    /**
     * Sets the copyright information for the article.
     *
     * @param Small $copyright - The copyright information.
     *
     * @return $this
     */
    public function withCopyright(Small $copyright): this
    {
        $this->copyright = $copyright;

        return $this;
    }

    /**
     * Sets the related articles within the footer of the article.
     *
     * @param RelatedArticles $related_articles - The related articles
     *
     * @return $this
     */
    public function withRelatedArticles(RelatedArticles $related_articles): this
    {
        $this->relatedArticles = $related_articles;

        return $this;
    }

    /**
     * Gets the text content of the credits
     *
     * @return vec<string|Paragraph> A list of paragraphs or a single string for the content of the credit.
     */
    public function getCredits(): vec<mixed>
    {
        return $this->credits;
    }

    /**
     * Gets the copyright information for the article.
     *
     * @return Small The copyright information.
     */
    public function getCopyright(): ?Small
    {
        return $this->copyright;
    }

    /**
     * Gets the related articles within the footer of the article.
     *
     * @return RelatedArticles The related articles
     */
    public function getRelatedArticles(): ?RelatedArticles
    {
        return $this->relatedArticles;
    }

    /**
     * Structure and create the full Footer in a DOMNode.
     *
     * @param \DOMDocument $document - The document where this element will be appended (optional).
     *
     * @return \DOMNode
     */
    public function toDOMElement(\DOMDocument $document): \DOMNode
    {
        $footer = $document->createElement('footer');

        // Footer markup
        if ($this->credits) {
            $aside = $document->createElement('aside');
            foreach ($this->credits as $credit) {
                if ($credit instanceof Paragraph) {
                    Element::appendChild($aside, $credit, $document);
                } elseif (is_string($credit)) {
                    $aside->appendChild($document->createTextNode($credit));
                }
            }
            $footer->appendChild($aside);
        }

        Element::appendChild($footer, $this->copyright, $document);

        Element::appendChild($footer, $this->relatedArticles, $document); 

        if (!$this->credits && !$this->copyright && !$this->relatedArticles) {
            $footer->appendChild($document->createTextNode(''));
        }

        return $footer;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid Footer when it is filled, false otherwise.
     */
    public function isValid(): bool
    {
        return
            $this->credits ||
            $this->copyright ||
            $this->relatedArticles;
    }

    /**
     * Implements the ChildrenContainer::getContainerChildren().
     *
     * @see ChildrenContainer::getContainerChildren()
     * @return vec<Element> of Paragraph|RelatedArticles
     */
    public function getContainerChildren(): vec<Element>
    {
        $children = vec[];

        if ($this->credits) {
            foreach ($this->credits as $credit) {
                if ($credit instanceof Element) {
                    $children[] = $credit;
                }
            }
        }

        if ($this->relatedArticles) {
            $children[] =  $this->relatedArticles;
        }

        return $children;
    }
}
