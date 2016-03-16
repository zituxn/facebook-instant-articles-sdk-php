<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Map;
use Facebook\InstantArticles\Elements\Caption;
use Facebook\InstantArticles\Elements\InstantArticle;

class MapRule extends ConfigurationSelectorRule
{
    const PROPERTY_MAP_GEOTAG = 'map.geotag';
    const PROPERTY_CAPTION = 'map.caption';
    const PROPERTY_AUDIO = 'map.audio';

    public function getContextClass()
    {
        return InstantArticle::class;
    }

    public static function create()
    {
        return new MapRule();
    }

    public static function createFrom($configuration)
    {
        $map_rule = self::create();
        $map_rule->withSelector($configuration['selector']);

        $map_rule->withProperties(
            array(
                self::PROPERTY_MAP_GEOTAG,
                self::PROPERTY_AUDIO,
                self::PROPERTY_CAPTION
            ),
            $configuration
        );

        return $map_rule;
    }

    public function apply($transformer, $instant_article, $node)
    {
        $map = Map::create();

        // Builds the image
        $geotag_node = $this->getProperty(self::PROPERTY_MAP_GEOTAG, $node);
        if ($geotag_node) {
            $instant_article->addChild($map);
            $transformer->transform($map, $node);
        } else {
            throw new \InvalidArgumentException('Invalid selector for '.self::PROPERTY_MAP_GEOTAG);
        }

        $audio_node = $this->getProperty(self::PROPERTY_AUDIO, $node);
        if ($audio_node) {
            $transformer->transform($map, $node);
        }
        $caption_node = $this->getProperty(self::PROPERTY_CAPTION, $node);
        if ($caption_node) {
            $transformer->transform($map, $node);
        }

        return $instant_article;
    }
}
