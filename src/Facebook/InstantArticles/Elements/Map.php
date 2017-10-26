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
 * Class Map
 * This element Class holds map content for the articles.
 *
 * Example:
 *  <figure class="op-map">
 *    <script type="application/json" class="op-geoTag">
 *      {
 *          "type": "Feature",
 *          "geometry": {
 *               "type": "Point",
 *               "coordinates": [23.166667, 89.216667]
 *          },
 *          "properties": {
 *               "title": "Jessore, Bangladesh",
 *               "radius": 750000,
 *               "pivot": true,
 *               "style": "satellite",
 *           }
 *       }
 *    </script>
 *  </figure>
 *
 */
class Map extends Audible implements ChildrenContainer
{
    /**
     * @var Caption The caption for Image
     */
    private ?Caption $caption;

    /**
     * @var GeoTag The json geoTag content inside the script geoTag
     */
    private ?GeoTag $geoTag;

    /**
     * @var Audio The audio file for this Image
     */
    private ?Audio $audio;

    private function __construct()
    {
    }

    /**
     * Factory method for the Map
     *
     * @return Map the new instance
     */
    public static function create(): \Facebook\InstantArticles\Elements\Map
    {
        return new self();
    }

    /**
     * This sets figcaption tag as documentation. It overrides all sets
     * made with Caption.
     *
     * @see Caption.
     * @param Caption $caption the caption the map will have
     *
     * @return $this
     */
    public function withCaption(Caption $caption): \Facebook\InstantArticles\Elements\Map
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Sets the geoTag on the image.
     *
     * @param GeoTag $geo_tag The tag to be set on the map object
     *
     * @see {link:http://geojson.org/}
     *
     * @return $this
     */
    public function withGeoTag(GeoTag $geo_tag): \Facebook\InstantArticles\Elements\Map
    {
        $this->geoTag = $geo_tag;

        return $this;
    }

    /**
     * Adds audio to this image.
     *
     * @param Audio $audio The audio object
     *
     * @return $this
     */
    public function withAudio(Audio $audio): Audible
    {
        $this->audio = $audio;

        return $this;
    }

    /**
     * @return Caption the caption for the Map
     */
    public function getCaption(): ?Caption
    {
        return $this->caption;
    }

    /**
     * @return string Geotag json content unescaped
     */
    public function getGeotag(): ?GeoTag
    {
        return $this->geoTag;
    }

    /**
     * @return Audio the audio object
     */
    public function getAudio(): ?Audio
    {
        return $this->audio;
    }

    /**
     * Structure and create the full Map in a XML format DOMNode.
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
        $element->setAttribute('class', 'op-map');

        // Geotag markup REQUIRED
        if ($this->geoTag) {
            $element->appendChild($this->geoTag->toDOMElement($document));
        }

        // Caption markup optional
        if ($this->caption) {
            $element->appendChild($this->caption->toDOMElement($document));
        }

        // Audio markup optional
        if ($this->audio) {
            $element->appendChild($this->audio->toDOMElement($document));
        }

        return $element;
    }

    /**
     * Overrides the Element::isValid().
     *
     * @see Element::isValid().
     * @return true for valid Map that contains valid GeoTag, false otherwise.
     */
    public function isValid(): bool
    {
        return $this->geoTag && $this->geoTag->isValid();
    }

    /**
     * Implements the Container::getContainerChildren().
     *
     * @see Container::getContainerChildren().
     * @return array of Elements contained by Image.
     */
    public function getContainerChildren(): Vector<Element>
    {
        $children = Vector {};

        if ($this->caption) {
            $children->add($this->caption);
        }

        // Geotag markup optional
        if ($this->geoTag) {
            $children->add($this->geoTag);
        }

        // Audio markup optional
        if ($this->audio) {
            $children->add($this->audio);
        }

        return $children;
    }
}
