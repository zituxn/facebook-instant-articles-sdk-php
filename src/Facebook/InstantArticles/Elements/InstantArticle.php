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
  * Class InstantArticle
  * This class holds the content of one InstantArticle and is children
  *
  *    <html>
  *        <head>
  *            ...
  *        </head>
  *        <body>
  *            <article>
  *                <header>
  *                    <figure>...</figure>
  *                    <h1>...</h1>
  *                    <time>...</time>
  *                </header>
  *                <contents...>
  *            </article>
  *        </body>
  *    </html>
  *
*/
class InstantArticle extends Element
{
    /**
     * @var string The canonical URL for the Instant Article
     */
    private $canonicalURL;

    /**
     * @var string The markup version for this InstantArticle
     */
    private $markupVersion = 'v1.0';

    /**
     * @var boolean The ad strategy that will be used. True by default
     */
    private $isAutomaticAdPlaced = true;

    /**
     * @var string The charset that will be used. "utf-8" by default.
     */
    private $charset = 'utf-8';

    /**
     * @var ArticleHeader element to hold header content, like images etc
     */
    private $header;

    /**
     * @var ArticleFooter element to hold footer content.
     */
    private $footer;

    /**
     * @var Element[] of all elements an article can have.
     */
    private $children = array();

    /**
     * Factory method
     * @return InstantArticle object.
     */
    public static function create()
    {
        return new InstantArticle();
    }

    /**
     * Private constructor. It must be used the Factory method
     * @see InstantArticle#create() For building objects
     * @return InstantArticle object.
     */
    private function __construct()
    {
        $this->header = Header::create();
    }

    /**
     * Sets the canonical URL for the Instant Article. It is REQUIRED.
     *
     * @param string The canonical url of article. Ie: http://domain.com/article.html
     */
    public function withCanonicalURL($url)
    {
        Type::enforce($url, Type::STRING);
        $this->canonicalURL = $url;

        return $this;
    }

    /**
     * Sets the charset for the Instant Article. utf-8 by default.
     *
     * @param string The charset of article. Ie: "iso-8859-1"
     */
    public function withCharset($charset)
    {
        Type::enforce($charset, Type::STRING);
        $this->charset = $charset;

        return $this;
    }

    /**
     * Use the strategy of auto ad placement
     */
    public function enableAutomaticAdPlacement()
    {
        $this->isAutomaticAdPlaced = true;
        return $this;
    }

    /**
     * Use the strategy of manual ad placement
     */
    public function disableAutomaticAdPlacement()
    {
        $this->isAutomaticAdPlaced = false;
        return $this;
    }

    /**
     * Sets the header content to this InstantArticle
     * @param Header to be added to this Article.
     */
    public function withHeader($header)
    {
        Type::enforce($header, Header::class);
        $this->header = $header;

        return $this;
    }

    /**
     * Sets the footer content to this InstantArticle
     * @param Footer to be added to this Article.
     */
    public function withFooter($footer)
    {
        Type::enforce($footer, Footer::class);
        $this->footer = $footer;

        return $this;
    }

    /**
     * Adds new child elements to this InstantArticle
     * @param Element to be added to this Article.
     */
    public function addChild($child)
    {
        Type::enforce(
            $child,
            array(
                Ad::class,
                Analytics::class,
                AnimatedGif::class,
                Audio::class,
                Blockquote::class,
                Image::class,
                H1::class,
                H2::class,
                Interactive::class,
                ListElement::class,
                Map::class,
                Paragraph::class,
                Pullquote::class,
                RelatedArticles::class,
                Slideshow::class,
                SocialEmbed::class,
                Video::class
            )
        );
        $this->children[] = $child;

        return $this;
    }

    /**
     * @return Header header element from the InstantArticle
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return Footer footer element from the InstantArticle
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * @return array<Element> the elements this article contains
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function toDOMElement($document = null)
    {
        if (!$document) {
            $document = new \DOMDocument();
        }

        // Builds and appends head to the HTML document
        $html = $document->createElement('html');
        $head = $document->createElement('head');

        $link = $document->createElement('link');
        $link->setAttribute('rel', 'canonical');
        $link->setAttribute('href', $this->canonicalURL);
        $head->appendChild($link);

        $charset = $document->createElement('meta');
        $charset->setAttribute('charset', $this->charset);
        $head->appendChild($charset);

        $markup_version = $document->createElement('meta');
        $markup_version->setAttribute('property', 'op:markup_version');
        $markup_version->setAttribute('content', $this->markupVersion);
        $head->appendChild($markup_version);

        $ad_placement = $document->createElement('meta');
        $ad_placement->setAttribute('property', 'fb:use_automatic_ad_placement');
        $ad_placement->setAttribute('content', $this->isAutomaticAdPlaced ? 'true' : 'false');
        $head->appendChild($ad_placement);

        $html->appendChild($head);

        // Build and append body and article tags to the HTML document
        $body = $document->createElement('body');
        $article = $document->createElement('article');
        $body->appendChild($article);
        $html->appendChild($body);
        if ($this->header) {
            $article->appendChild($this->header->toDOMElement($document));
        }
        if ($this->children) {
            foreach ($this->children as $child) {
                $article->appendChild($child->toDOMElement($document));
            }
            if ($this->footer) {
                $article->appendChild($this->footer->toDOMElement($document));
            }
        } else {
            $article->appendChild($document->createTextNode(''));
        }

        return $html;
    }
}
