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
 * Class Video
 * This element Class is the video for the article.
 * Also consider to use one of the other media types for an article:
 * <ul>
 *     <li>Audio</li>
 *     <li>Image</li>
 *     <li>Slideshow</li>
 *     <li>Map</li>
 * </ul>
 *
 * Example:
 *  <figure>
 *      <video>
 *          <source src="http://mydomain.com/path/to/video.mp4" type="video/mp4" />
 *      </video>
 *  </figure>
 *
 * @see Audio
 * @see Image
 * @see Slideshow
 * @see Map
 * @package Facebook\InstantArticle\Elements
 */
class Video extends Element implements ChildrenContainer, GeoTaggable, Captionable
{
    const ASPECT_FIT = 'aspect-fit';
    const ASPECT_FIT_ONLY = 'aspect-fit-only';
    const FULLSCREEN = 'fullscreen';
    const NON_INTERACTIVE = 'non-interactive';

    const LOOP = 'loop';
    const DATA_FADE = 'data-fade';

    /**
     * @var boolean marks if any created image will have likes enabled by default
     */
    private static bool $defaultLikeEnabled = false;

    /**
     * @var boolean marks if any created image will have comments enabled by default
     */
    private static bool $defaultCommentEnabled = false;

    /**
     * @var Caption The caption for Video
     */
    private ?Caption $caption;

    /**
     * @var string The string url for the video hosted on web that will be shown
     * on the article
     */
    private string $url = "";

    /**
     * @var string The video content type.
     */
    private string $contentType = "";

    /**
     * @var boolean Tells if like is enabled. Default: false
     */
    private bool $isLikeEnabled;

    /**
     * @var boolean Tells if comments are enabled. Default: false
     */
    private bool $isCommentsEnabled;

    /**
     * @var boolean Makes the video the cover on news feed.
     *
     * @see {link:https://developers.facebook.com/docs/instant-articles/reference/feed-preview}
     */
    private bool $isFeedCover = false;

    /**
     * @var string Content that will be shown on <cite>...</cite> tags.
     */
    private string $attribution = "";

    /**
     * @var string The picture size for the video.
     *
     * @see Video::ASPECT_FIT
     * @see Video::ASPECT_FIT_ONLY
     * @see Video::FULLSCREEN
     * @see Video::NON_INTERACTIVE
     */
    private string $presentation = "";

    /**
     * @var GeoTag The json geotag content inside the script geotag
     */
    private ?GeoTag $geoTag;

    /**
     * @var string URL for the placeholder Image that will be placed while video not loaded.
     */
    private string $imageURL = "";

    /**
     * @var boolean Default true, so every video will autoplay.
     */
    private bool $isAutoplay = true;

    /**
     * @var boolean Default false, so every video will have no controls.
     */
    private bool $isControlsShown = false;

    private function __construct()
    {
        $this->isLikeEnabled = self::$defaultLikeEnabled;
        $this->isCommentsEnabled = self::$defaultCommentEnabled;
    }

    /**
     * Factory method
     *
     * @return Video the new instance from Video
     */
    public static function create(): Video
    {
        return new self();
    }

    /**
     * This sets figcaption tag as documentation. It overrides all sets
     * made with Caption.
     *
     * @param Caption $caption the caption the video will have
     * @see Caption
     * @return $this
     */
    public function withCaption(Caption $caption): this
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * Sets the URL for the video. It is REQUIRED.
     *
     * @param string $url The url of video. Ie: http://domain.com/video.mp4
     *
     * @return $this
     */
    public function withURL(string $url): this
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Sets the aspect ration presentation for the video.
     *
     * @param string $presentation one of the constants ASPECT_FIT, ASPECT_FIT_ONLY, FULLSCREEN or NON_INTERACTIVE
     *
     * @see Video::ASPECT_FIT
     * @see Video::ASPECT_FIT_ONLY
     * @see Video::FULLSCREEN
     * @see Video::NON_INTERACTIVE
     *
     * @return $this
     */
    public function withPresentation(string $presentation): this
    {
        Type::enforceWithin(
            $presentation,
            vec[
                Video::ASPECT_FIT,
                Video::ASPECT_FIT_ONLY,
                Video::FULLSCREEN,
                Video::NON_INTERACTIVE,
            ]
        );
        $this->presentation = $presentation;

        return $this;
    }

    /**
     * Makes like enabled for this video.
     *
     * @return $this
     */
    public function enableLike(): this
    {
        $this->isLikeEnabled = true;
        return $this;
    }

    /**
     * Makes like disabled for this video.
     *
     * @return $this
     */
    public function disableLike(): this
    {
        $this->isLikeEnabled = false;
        return $this;
    }

    /**
     * Makes comments enabled for this video.
     *
     * @return $this
     */
    public function enableComments(): this
    {
        $this->isCommentsEnabled = true;
        return $this;
    }

    /**
     * Makes comments disabled for this video.
     *
     * @return $this
     */
    public function disableComments(): this
    {
        $this->isCommentsEnabled = false;
        return $this;
    }

    /**
     * Enables the video controls
     *
     * @return $this
     */
    public function enableControls(): this
    {
        $this->isControlsShown = true;

        return $this;
    }

    /**
     * Disable the video controls
     *
     * @return $this
     */
    public function disableControls(): this
    {
        $this->isControlsShown = false;
        return $this;
    }

    /**
     * Enables the video autoplay
     *
     * @return $this
     */
    public function enableAutoplay(): this
    {
        $this->isAutoplay = true;
        return $this;
    }

    /**
     * Disable the video autoplay
     *
     * @return $this
     */
    public function disableAutoplay(): this
    {
        $this->isAutoplay = false;
        return $this;
    }

    /**
     * Makes video be the cover on newsfeed
     *
     * @return $this
     */
    public function enableFeedCover(): this
    {
        $this->isFeedCover = true;
        return $this;
    }

    /**
     * Removes video from cover on newsfeed (and it becomes the og:image that was already defined on the link)
     *
     * @return $this
     */
    public function disableFeedCover(): this
    {
        $this->isFeedCover = false;
        return $this;
    }


    /**
     * @param string $contentType content type of the video. Ex: "video/mp4"
     *
     * @return $this
     */
    public function withContentType(string $contentType): this
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * Sets the geotag on the video.
     *
     * @see {link:http://geojson.org/}
     *
     * @param string $geoTag
     *
     * @return $this
     */
    public function withGeoTag(GeoTag $geoTag): this
    {
        $this->geoTag = $geoTag;
        return $this;
    }

    /**
     * Sets the attribution string
     *
     * @param string $attribution The attribution text
     *
     * @return $this
     */
    public function withAttribution(string $attribution): this
    {
        $this->attribution = $attribution;
        return $this;
    }

    /**
     * @return Caption gets the caption obj
     */
    public function getCaption(): ?Caption
    {
        return $this->caption;
    }

    /**
     * @return string URL gets the image url
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string The content-type of video
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @return boolean tells if the like button is enabled
     */
    public function isLikeEnabled(): bool
    {
        return $this->isLikeEnabled;
    }

    /**
     * @return boolean tells if the autoplay is enabled
     */
    public function isAutoplay(): bool
    {
        return $this->isAutoplay;
    }

    /**
     * @return boolean tells if the comments widget is enabled
     */
    public function isCommentsEnabled(): bool
    {
        return $this->isCommentsEnabled;
    }

    /**
     * @return boolean tells if the controls will be shown
     */
    public function isControlsShown(): bool
    {
        return $this->isControlsShown;
    }

    /**
     * @return string one of the constants ASPECT_FIT, ASPECT_FIT_ONLY, FULLSCREEN or NON_INTERACTIVE
     *
     * @see Video::ASPECT_FIT
     * @see Video::ASPECT_FIT_ONLY
     * @see Video::FULLSCREEN
     * @see Video::NON_INTERACTIVE
     */
    public function getPresentation(): string
    {
        return $this->presentation;
    }

    /**
     * @return GeoTag The geotag content
     */
    public function getGeotag(): ?GeoTag
    {
        return $this->geoTag;
    }

    /**
     * Modify the default setup to enable/disable likes in videos
     *
     * WARNING this is not Thread-safe, so if you are using pthreads or any other multithreaded engine,
     * this might not work as expected. (you will need to set this in all working threads manually)
     * @param boolean $enabled inform true to enable likes on videos per default or false to disable like on videos.
     */
    public static function setDefaultLikeEnabled(bool $enabled): void
    {
        self::$defaultLikeEnabled = $enabled;
    }

    /**
     * Modify the default setup to enable/disable comments in videos
     *
     * WARNING this is not Thread-safe, so if you are using pthreads or any other multithreaded engine,
     * this might not work as expected. (you will need to set this in all working threads manually)
     * @param boolean $enabled inform true to enable comments on videos per default or false to disable commenting on videos.
     */
    public static function setDefaultCommentEnabled(bool $enabled): void
    {
        self::$defaultCommentEnabled = $enabled;
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

        $element = $document->createElement('figure');

        // Presentation
        if (!Type::isTextEmpty($this->presentation)) {
            $element->setAttribute('data-mode', $this->presentation);
        }

        // Poster frame / Image placeholder
        if (!Type::isTextEmpty($this->imageURL)) {
            $imageElement = $document->createElement('img');
            $imageElement->setAttribute('src', $this->imageURL);
            $element->appendChild($imageElement);
        }

        if ($this->isFeedCover) {
            $element->setAttribute('class', 'fb-feed-cover');
        }

        // Like/comments markup optional
        if ($this->isLikeEnabled || $this->isCommentsEnabled) {
            if ($this->isLikeEnabled && $this->isCommentsEnabled) {
                $element->setAttribute('data-feedback', 'fb:likes,fb:comments');
            } elseif ($this->isLikeEnabled) {
                $element->setAttribute('data-feedback', 'fb:likes');
            } else {
                $element->setAttribute('data-feedback', 'fb:comments');
            }
        }

        // URL markup required
        if (!Type::isTextEmpty($this->url)) {
            $videoElement = $document->createElement('video');
            if (!$this->isAutoplay) {
                $videoElement->setAttribute('data-fb-disable-autoplay', 'data-fb-disable-autoplay');
            }
            if ($this->isControlsShown) {
                $videoElement->setAttribute('controls', 'controls');
            }
            $sourceElement = $document->createElement('source');
            $sourceElement->setAttribute('src', $this->url);
            if ($this->contentType) {
                $sourceElement->setAttribute('type', $this->contentType);
            }
            $videoElement->appendChild($sourceElement);
            $element->appendChild($videoElement);
        }

        // Caption markup optional
        if ($this->caption) {
            $element->appendChild($this->caption->toDOMElement($document));
        }

        // Geotag markup optional
        if ($this->geoTag) {
            $element->appendChild($this->geoTag->toDOMElement($document));
        }

        // Attribution Citation
        if (!Type::isTextEmpty($this->attribution)) {
            $attributionElement = $document->createElement('cite');
            $attributionElement->appendChild($document->createTextNode($this->attribution));
            $element->appendChild($attributionElement);
        }

        return $element;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid Video that contains not empty url, false otherwise.
     */
    public function isValid(): bool
    {
        return !Type::isTextEmpty($this->url);
    }

    /**
     * Implements the ChildrenContainer::getContainerChildren().
     *
     * @see ChildrenContainer::getContainerChildren().
     * @return vec of Elements contained by Video.
     */
    public function getContainerChildren(): vec<Element>
    {
        $children = vec[];

        if ($this->caption) {
            $children[] = $this->caption;
        }

        // Geotag markup optional
        if ($this->geoTag) {
            $children[] = $this->geoTag;
        }

        return $children;
    }
}
