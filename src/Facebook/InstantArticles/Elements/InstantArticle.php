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

class InstantArticle extends Element implements ChildrenContainer, InstantArticleInterface
{
    const CURRENT_VERSION = '1.6.2';

    /**
     * The meta properties that are used on <head>
     */
    private dict<string, string> $metaProperties = dict[];

    /**
     * @var string The canonical URL for the Instant Article
     */
    private string $canonicalURL = "";

    /**
     * @var string The markup version for this InstantArticle
     */
    private string $markupVersion = 'v1.0';

    /**
     * @var boolean The ad strategy that will be used. True by default
     */
    private bool $isAutomaticAdPlaced = true;

    /**
     * @var string The ad density that will be used. "default" by default
     */
    private string $adDensity = 'default';

    /**
     * @var string The charset that will be used. "utf-8" by default.
     */
    private string $charset = 'utf-8';

    /**
     * @var string|null The style that will be applied to the article. Optional.
     */
    private string $style = "";

    /**
     * @var Header element to hold header content, like images etc
     */
    private ?Header $header;

    /**
     * @var Footer element to hold footer content.
     */
    private ?Footer $footer;

    /**
     * @var Element[] of all elements an article can have.
     */
    private vec<Element> $children = vec[];

    /**
     * @var boolean flag that indicates if this article is Right-to-left(RTL). Defaults to false.
     */
    private bool $isRTLEnabled = false;

    /**
     * Factory method
     * @return InstantArticle object.
     */
    public static function create(): InstantArticle
    {
        return new InstantArticle();
    }

    /**
     * Private constructor. It must be used the Factory method
     * @see InstantArticle#create() For building objects
     */
    private function __construct()
    {
        $this->header = Header::create();
        $this->addMetaProperty('op:generator', 'facebook-instant-articles-sdk-php');
        $this->addMetaProperty('op:generator:version', self::CURRENT_VERSION);
    }

    /**
     * Sets the canonical URL for the Instant Article. It is REQUIRED.
     *
     * @param string $url The canonical url of article. Ie: http://domain.com/article.html
     *
     * @return $this
     */
    public function withCanonicalUrl(string $url): this
    {
        $this->canonicalURL = $url;

        return $this;
    }

    /**
     * Sets the charset for the Instant Article. utf-8 by default.
     *
     * @param string $charset The charset of article. Ie: "iso-8859-1"
     *
     * @return $this
     */
    public function withCharset(string $charset): this
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * Sets the style to be applied to this Instant Article
     *
     * @param string $style Name of the style
     *
     * @return $this
     */
    public function withStyle(string $style): this
    {
        $this->style = $style;

        return $this;
    }

    /**
     * Use the strategy of auto ad placement
     */
    public function enableAutomaticAdPlacement(): this
    {
        $this->isAutomaticAdPlaced = true;
        return $this;
    }

    /**
     * Use the strategy of manual ad placement
     */
    public function disableAutomaticAdPlacement(): this
    {
        $this->isAutomaticAdPlaced = false;
        return $this;
    }

    /**
     * Sets the ad density to be used for auto ad placement
     *
     * @param string $adDensity Ad density
     *
     * @return $this
     */
    public function withAdDensity(string $adDensity): this
    {
        $this->adDensity = $adDensity;

        return $this;
    }

    /**
     * Updates article to use RTL orientation.
     */
    public function enableRTL(): this
    {
        $this->isRTLEnabled = true;
        return $this;
    }

    /**
     * Updates article to use LTR orientation (default), disabling RTL.
     */
    public function disableRTL(): this
    {
        $this->isRTLEnabled = false;
        return $this;
    }

    /**
     * Sets the header content to this InstantArticle
     *
     * @param Header $header to be added to this Article.
     *
     * @return $this
     */
    public function withHeader(Header $header): this
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Sets the footer content to this InstantArticle
     *
     * @param Footer $footer to be added to this Article.
     *
     * @return $this
     */
    public function withFooter(Footer $footer): this
    {
        $this->footer = $footer;

        return $this;
    }

    /**
     * Replace all the children within this InstantArticle
     *
     * @param vec<Element> $children Array of elements replacing the original.
     *
     * @return $this
     */
    public function withChildren(vec<Element> $children): this
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Replace all the children within this InstantArticle
     *
     * @param int $index The index of the element to be deleted
     *                             in the array of children.
     *
     * @return $this
     */
    public function deleteChild(int $index): this
    {
        // per https://github.com/facebook/hhvm/issues/6451#issue-114335255
        $newChildren = vec[];
        foreach($this->children as $key => $child) {
            if ($key !== $index) {
                $newChildren[] = $child;
            }
        };
        $this->children = $newChildren;

        return $this;
    }

    /**
     * Adds new child elements to this InstantArticle
     *
     * @param Element $child to be added to this Article.
     *
     * @return $this
     */
    public function addChild(Element $child): this
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Adds new child elements to the front of this InstantArticle
     *
     * @param Element to be added to this Article.
     *
     * @return $this
     */
    public function unshiftChild(Element $child): this
    {
        array_unshift($this->children, $child);

        return $this;
    }

    /**
     * @return string canonicalURL from the InstantArticle
     */
    public function getCanonicalURL(): string
    {
        return $this->canonicalURL;
    }

    /**
     * @return string style from the InstantArticle
     */
    public function getStyle(): string
    {
        return $this->style;
    }

    /**
     * @return Header header element from the InstantArticle
     */
    public function getHeader(): ?Header
    {
        return $this->header;
    }

    /**
     * @return Footer footer element from the InstantArticle
     */
    public function getFooter(): ?Footer
    {
        return $this->footer;
    }

    /**
     * @return vec<Element> the elements this article contains
     */
    public function getChildren(): vec<Element>
    {
        return $this->children;
    }

    /**
     * @return boolean if this article is Right-to-left(RTL).
     */
    public function isRTLEnabled(): bool
    {
        return $this->isRTLEnabled;
    }

    /**
     * @return string The article charset.
     */
    public function getCharset(): string
    {
        return $this->charset;
    }

    /**
     * Adds a meta property for the <head> of Instant Article.
     *
     * @param string $property_name name of meta attribute
     * @param string $property_content content of meta attribute
     *
     * @return $this
     */
    public function addMetaProperty(string $property_name, string $property_content): this
    {
        $this->metaProperties[$property_name] = $property_content;
        return $this;
    }

    public function render(string $doctype = '<!doctype html>', bool $format = false): string
    {
        $doctype = is_null($doctype) ? '<!doctype html>' : $doctype;
        return parent::render($doctype, $format);
    }

    public function toDOMElement(\DOMDocument $document): \DOMNode
    {
        // Builds and appends head to the HTML document
        $html = $document->createElement('html');
        if ($this->isRTLEnabled) {
            $html->setAttribute('dir', 'rtl');
        }
        $head = $document->createElement('head');
        $html->appendChild($head);

        $link = $document->createElement('link');
        $link->setAttribute('rel', 'canonical');
        $link->setAttribute('href', $this->canonicalURL ?: '');
        $head->appendChild($link);

        $charset = $document->createElement('meta');
        $charset->setAttribute('charset', $this->charset);
        $head->appendChild($charset);

        $this->addMetaProperty('op:markup_version', $this->markupVersion);
        if ($this->header && count($this->header->getAds()) > 0) {
            if ($this->isAutomaticAdPlaced) {
                $this->addMetaProperty(
                    'fb:use_automatic_ad_placement',
                    'enable=true ad_density=' . $this->adDensity
                );
            } else {
                $this->addMetaProperty('fb:use_automatic_ad_placement', 'false');
            }
        }

        if ($this->style) {
            $this->addMetaProperty('fb:article_style', $this->style);
        }

        // Adds all meta properties
        foreach ($this->metaProperties as $property_name => $property_content) {
            $head->appendChild(
                $this->createMetaElement(
                    $document,
                    $property_name,
                    $property_content
                )
            );
        }

        // Build and append body and article tags to the HTML document
        $body = $document->createElement('body');
        $article = $document->createElement('article');
        $body->appendChild($article);
        $html->appendChild($body);
        if ($this->header && $this->header->isValid()) {
            $article->appendChild($this->header?->toDOMElement($document));
        }
        if ($this->children) {
            foreach ($this->children as $child) {
                if ($child instanceof TextContainer) {
                    if (count($child->getTextChildren()) === 0) {
                        continue;
                    } elseif (count($child->getTextChildren()) === 1) {
                        if (is_string($child->getTextChildren()[0]) &&
                            trim($child->getTextChildren()[0]) === '') {
                            continue;
                        }
                    }
                }
                $article->appendChild($child->toDOMElement($document));
            }
            if ($this->footer && $this->footer->isValid()) {
                $article->appendChild($this->footer?->toDOMElement($document));
            }
        } else {
            $article->appendChild($document->createTextNode(''));
        }

        return $html;
    }

    private function createMetaElement(\DOMDocument $document, string $property_name, string $property_content): \DOMNode
    {
        $element = $document->createElement('meta');
        $element->setAttribute('property', $property_name);
        $element->setAttribute('content', $property_content);
        return $element;
    }

    public function isValid(): bool
    {
        $header_valid = false;
        if ($this->header) {
            $header_valid = $this->header->isValid();
        }

        $items = $this->getChildren();
        $one_item_valid = false;
        if ($items) {
            foreach ($items as $item) {
                if ($item->isValid()) {
                    $one_item_valid = true;
                    break;
                }
            }
        }

        $footer_valid = true;
        if ($this->footer) {
            $footer_valid = $this->footer->isValid();
        }

        return
            $this->canonicalURL &&
            !Type::isTextEmpty($this->canonicalURL) &&
            $header_valid &&
            $footer_valid &&
            $one_item_valid;
    }

    public function getContainerChildren(): vec<Element>
    {
        $children = vec[];

        $header = $this->getHeader();
        if ($header) {
            $children[] = $header;
        }

        $items = $this->getChildren();
        if ($items) {
            foreach ($items as $item) {
                $children[] = $item;
            }
        }

        $footer = $this->getFooter();
        if ($footer) {
            $children[] = $footer;
        }

        return $children;
    }

    public function getFirstParagraph(): Paragraph
    {
        $items = $this->getChildren();
        if ($items) {
            foreach ($items as $item) {
                if ($item instanceof Paragraph) {
                    return $item;
                }
            }
        }
        // Case no paragraph exists, we return an empty paragraph
        return Paragraph::create();
    }
}
