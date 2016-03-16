<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Elements\Slideshow;
use Facebook\InstantArticles\Transformer\Getters\GetterFactory;
use Facebook\InstantArticles\Transformer\Getters\StringGetter;
use Facebook\InstantArticles\Transformer\Getters\ChildrenGetter;

class SlideshowRule extends ConfigurationSelectorRule
{
    const PROPERTY_AUDIO = 'slideshow.audio';
    const PROPERTY_CAPTION = 'slideshow.caption';

    public function getContextClass()
    {
        return InstantArticle::class;
    }

    public static function create()
    {
        return new SlideshowRule();
    }

    public static function createFrom($configuration)
    {
        $slideshow_rule = self::create();
        $slideshow_rule->withSelector($configuration['selector']);

        $slideshow_rule->withProperties(
            array(
                self::PROPERTY_AUDIO,
                self::PROPERTY_CAPTION
            ),
            $configuration
        );


        return $slideshow_rule;
    }

    public function apply($transformer, $instant_article, $node)
    {
        // Builds the slideshow
        $slideshow = Slideshow::create();
        $instant_article->addChild($slideshow);
        $transformer->transform($slideshow, $node);

        $audio_node = $this->getProperty(self::PROPERTY_AUDIO, $node);
        if ($audio_node) {
            $transformer->transform($slideshow, $node);
        }
        $caption_node = $this->getProperty(self::PROPERTY_CAPTION, $node);
        if ($caption_node) {
            $transformer->transform($slideshow, $node);
        }

        return $instant_article;
    }
}
