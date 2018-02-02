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
 * The header of the article. A header can hold an Image,
 * Title, Authors and Dates for publishing and modification of the article.
 *
 * <header>
 *     <figure>
 *         <ui:image src={$this->getHeroBackground()} />
 *     </figure>
 *     <h1>{$this->name}</h1>
 *     <address>
 *         <a rel="facebook" href="http://facebook.com/everton.rosario">Everton</a>
 *         Everton Rosario is a passionate mountain biker on Facebook
 *     </address>
 *     <time
 *         class="op-published"
 *         datetime={date('c', $this->time)}>
 *         {date('F jS, g:ia', $this->time)}
 *     </time>
 *     <time
 *         class="op-modified"
 *         datetime={date('c', $last_update)}>
 *         {date('F jS, g:ia', $last_update)}
 *     </time>
 * </header>
 */
class Header extends Element implements ChildrenContainer
{
    /**
     * @var Image|Video|Slideshow|null for the image or video on the header.
     *
     * @see Image
     * @see Slideshow
     * @see Video
     */
    private ?Element $cover;

    /**
     * H1 The title of the Article that will be displayed on header.
     */
    private ?H1 $title;

    /**
     * H2 The subtitle of the Article that will be displayed on header.
     */
    private ?H2 $subtitle;

    /**
     * @var vec<Author> Authors of the article.
     */
    private vec<Author> $authors = vec[];

    /**
     * @var Time of publishing for the article
     */
    private ?Time $published;

    /**
     * @var Time of modification of the article, if it has
     * updated.
     */
    private ?Time $modified;

    /**
     * @var H3 Header kicker
     */
    private H3 $kicker;

    /**
     * @var vec<Ad> Ads of the article.
     */
    private vec<Ad> $ads = vec[];

    /**
     * @var Sponsor The sponsor for this article. See Branded Content.
     */
    private ?Sponsor $sponsor;

    private function __construct()
    {
        $this->kicker = H3::create();
    }

    /**
     * @return Header
     */
    public static function create(): Header
    {
        return new self();
    }

    /**
     * Sets the cover of InstantArticle with Image or Video
     *
     * @param Image|Video|Slideshow $cover The cover for the header of the InstantArticle
     *
     * @return $this
     */
    public function withCover(Element $cover): this
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * Sets the title of InstantArticle
     *
     * @param H1 $title The title of the InstantArticle
     *
     * @return $this
     */
    public function withTitle(H1 $title): this
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Sets the subtitle of InstantArticle
     *
     * @param H2 $subtitle The subtitle of the InstantArticle
     *
     * @return $this
     */
    public function withSubTitle(H2 $subtitle): this
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Append another author to the article
     *
     * @param Author $author The author name
     *
     * @return $this
     */
    public function addAuthor(Author $author): this
    {
        $this->authors[] = $author;

        return $this;
    }

    /**
     * Replace all authors within this Article
     *
     * @param vec<Author> $authors All the authors
     *
     * @return $this
     */
    public function withAuthors(vec<Author> $authors): this
    {
        $this->authors = $authors;

        return $this;
    }

    /**
     * Sets the publish Time for this article. REQUIRED
     *
     * @param Time $published The time and date of publishing of this article. REQUIRED
     *
     * @return $this
     */
    public function withPublishTime(Time $published): this
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Sets the update Time for this article. Optional
     *
     * @param Time $modified The time and date that this article was modified. Optional
     *
     * @return $this
     */
    public function withModifyTime(Time $modified): this
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Sets the update Time for this article. Optional
     *
     * @param Time $time The time and date that this article was modified. Optional
     *
     * @return $this
     */
    public function withTime(Time $time): this
    {
        if ($time->getType() === Time::MODIFIED) {
            $this->withModifyTime($time);
        } else {
            $this->withPublishTime($time);
        }

        return $this;
    }

    /**
     * Kicker text for the article header.
     *
     * @param H3 The kicker text to be set
     *
     * @return $this
     */
    public function withKicker(H3 $kicker): this
    {
        $this->kicker = $kicker;
        $this->kicker->enableKicker();

        return $this;
    }

    /**
     * Append another ad to the article
     *
     * @param Ad $ad Code for displaying an ad
     *
     * @return $this
     */
    public function addAd(Ad $ad): this
    {
        $this->ads[] = $ad;

        return $this;
    }

    /**
     * Replace all ads within this Article
     *
     * @param vec<Ad> $ads All the ads
     *
     * @return $this
     */
    public function withAds(vec<Ad> $ads): this
    {
        $this->ads = $ads;

        return $this;
    }

    /**
     * Sets the sponsor for this Article.
     *
     * @param Sponsor $sponsor The sponsor of article to be set.
     *
     * @return $this
     */
    public function withSponsor(Sponsor $sponsor): this
    {
        $this->sponsor = $sponsor;

        return $this;
    }

    /**
     * @return Image|Slideshow|Video The cover for the header of the InstantArticle
     */
    public function getCover(): ?Element
    {
        return $this->cover;
    }

    /**
     * @return H1 $title The title of the InstantArticle
     */
    public function getTitle(): ?H1
    {
        return $this->title;
    }

    /**
     * @return H2 $subtitle The subtitle of the InstantArticle
     */
    public function getSubtitle(): ?H2
    {
        return $this->subtitle;
    }

    /**
     * @return vec<Author> All the authors
     */
    public function getAuthors(): vec<Author>
    {
        return $this->authors;
    }

    /**
     * @return Time The time and date of publishing of this article
     */
    public function getPublished(): ?Time
    {
        return $this->published;
    }

    /**
     * @return Time The time and date that this article was modified.
     */
    public function getModified(): ?Time
    {
        return $this->modified;
    }

    /**
     * @return string The kicker text to be set
     */
    public function getKicker(): ?H3
    {
        return $this->kicker;
    }

    /**
     * @return vec<Ad> All the ads
     */
    public function getAds(): vec<Ad>
    {
        return $this->ads;
    }

    /**
     * @return Sponsor the sponsor of this Article.
     */
    public function getSponsor(): ?Sponsor
    {
        return $this->sponsor;
    }

    /**
     * Structure and create the full ArticleImage in a XML format DOMNode.
     *
     * @param \DOMDocument $document where this element will be appended. Optional
     *
     * @return \DOMNode
     */
    public function toDOMElement(\DOMDocument $document): \DOMNode
    {
        $element = $document->createElement('header');

        Element::appendChild($element, $this->cover, $document);
        Element::appendChild($element, $this->title, $document);
        Element::appendChild($element, $this->subtitle, $document);
        Element::appendChild($element, $this->published, $document);
        Element::appendChild($element, $this->modified, $document);

        if ($this->authors) {
            foreach ($this->authors as $author) {
                Element::appendChild($element, $author, $document);
            }
        }

        if ($this->kicker && $this->kicker->isValid()) {
            $element->appendChild($this->kicker->toDOMElement($document));
        }

        if (count($this->ads) === 1) {
            $this->ads[0]->disableDefaultForReuse();
            Element::appendChild($element, $this->ads[0], $document);
        } elseif (count($this->ads) >= 2) {
            $ads_container = $document->createElement('section');
            $ads_container->setAttribute('class', 'op-ad-template');

            $default_is_set = false;
            $has_valid_ad = false;
            foreach ($this->ads as $ad) {
                if ($default_is_set) {
                    $ad->disableDefaultForReuse();
                }

                if ($ad->getIsDefaultForReuse()) {
                    $default_is_set = true;
                }

                if ($ad->isValid()) {
                    $ads_container->appendChild($ad->toDOMElement($document));
                    $has_valid_ad = true;
                }
            }
            if ($has_valid_ad) {
                $element->appendChild($ads_container);
            }
        }

        Element::appendChild($element, $this->sponsor, $document);

        return $element;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid tag, false otherwise.
     */
    public function isValid(): bool
    {
        $has_ad = count($this->ads) > 0;
        $has_valid_ad = false;
        if ($has_ad) {
            foreach ($this->ads as $ad) {
                if ($ad->isValid()) {
                    $has_valid_ad = true;
                    break;
                }
            }
        }
        return
            ($this->title && $this->title->isValid()) ||
             $has_valid_ad;
    }

    /**
     * Implements the ChildrenContainer::getContainerChildren().
     *
     * @see ChildrenContainer::getContainerChildren().
     * @return vec of Elements contained by Header.
     */
    public function getContainerChildren(): vec<Element>
    {
        $children = vec[];

        if ($this->cover) {
            $children[] = $this->cover;
        }

        if ($this->title) {
            $children[] = $this->title;
        }

        if ($this->subtitle) {
            $children[] = $this->subtitle;
        }

        if ($this->published) {
            $children[] = $this->published;
        }

        if ($this->modified) {
            $children[] = $this->modified;
        }

        if ($this->authors) {
            foreach ($this->authors as $author) {
                $children[] = $author;
            }
        }

        if ($this->kicker) {
            $children[] = $this->kicker;
        }

        if (count($this->ads) > 0) {
            foreach ($this->ads as $ad) {
                $children[] = $ad;
            }
        }

        if ($this->sponsor) {
            $children[] = $this->sponsor;
        }

        return $children;
    }
}
