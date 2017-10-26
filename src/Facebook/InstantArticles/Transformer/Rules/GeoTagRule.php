<?hh
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Element;
use Facebook\InstantArticles\Elements\Image;
use Facebook\InstantArticles\Elements\Video;
use Facebook\InstantArticles\Elements\Map;
use Facebook\InstantArticles\Elements\GeoTag;
use Facebook\InstantArticles\Elements\GeoTaggable;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;

class GeoTagRule extends ConfigurationSelectorRule
{
    const PROPERTY_MAP_GEOTAG = 'map.geotag';

    public function getContextClass(): Vector<string>
    {
        return Vector {
            Image::getClassName(),
            Video::getClassName(),
            Facbook\InstantArticles\Elements\Map::getClassName(),
        };
    }

    public static function create(): GeoTagRule
    {
        return new GeoTagRule();
    }

    public static function createFrom(Map $configuration): GeoTagRule
    {
        $geo_tag_rule = self::create();
        $geo_tag_rule->withSelector(Type::getMapString($configuration, 'selector'));

        $geo_tag_rule->withProperty(
            self::PROPERTY_MAP_GEOTAG,
            Type::mixedToArray(self::retrieveProperty($configuration, self::PROPERTY_MAP_GEOTAG))
        );

        return $geo_tag_rule;
    }

    public function apply(Transformer $transformer, Element $media_container, \DOMNode $node): Element
    {
        $geo_tag = GeoTag::create();

        // Builds the image
        $script = $this->getProperty(self::PROPERTY_MAP_GEOTAG, $node);
        if ($script) {
            invariant(is_string($script), 'Error, $script is not string.');
            $geo_tag->withScript($script);
            if ($media_container instanceof GeoTaggable) {
                $media_container->withGeoTag($geo_tag);
            }
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_MAP_GEOTAG,
                    $media_container,
                    $node,
                    $this
                )
            );
        }

        return $media_container;
    }
}
