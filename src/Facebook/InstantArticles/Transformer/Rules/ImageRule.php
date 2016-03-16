<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Image;
use Facebook\InstantArticles\Elements\Caption;
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Transformer\Getters\GetterFactory;
use Facebook\InstantArticles\Transformer\Getters\StringGetter;
use Facebook\InstantArticles\Transformer\Getters\ChildrenGetter;

class ImageRule extends ConfigurationSelectorRule
{
    const PROPERTY_IMAGE_URL = 'image.url';
    const PROPERTY_CAPTION = 'image.caption';
    const PROPERTY_AUDIO = 'image.audio';
    const PROPERTY_MAP = 'image.map';
    const PROPERTY_LIKE = 'image.like';
    const PROPERTY_COMMENTS = 'image.comments';

    public function __construct()
    {
    }

    public function getContextClass()
    {
        return InstantArticle::class;
    }

    public static function create()
    {
        return new ImageRule();
    }

    public static function createFrom($configuration)
    {
        $image_rule = self::create();
        $image_rule->withSelector($configuration['selector']);

        $image_rule->withProperties(
            array(
                self::PROPERTY_IMAGE_URL,
                self::PROPERTY_CAPTION,
                self::PROPERTY_AUDIO,
                self::PROPERTY_MAP,
                self::PROPERTY_LIKE,
                self::PROPERTY_COMMENTS
            ),
            $configuration
        );

        return $image_rule;
    }

    public function apply($transformer, $instant_article, $node)
    {
        $image = Image::create();

        // Builds the image
        $url = $this->getProperty(self::PROPERTY_IMAGE_URL, $node);
        if ($url) {
            $image->withURL($url);
            $instant_article->addChild($image);
        } else {
            throw new \InvalidArgumentException('Invalid selector for '.self::PROPERTY_IMAGE_URL);
        }

        $caption_node = $this->getProperty(self::PROPERTY_CAPTION, $node);
        if ($caption_node) {
            $transformer->transform($image, $node);
        }

        $audio_node = $this->getProperty(self::PROPERTY_AUDIO, $node);
        if ($audio_node) {
            $transformer->transform($image, $node);
        }

        $map_node = $this->getProperty(self::PROPERTY_MAP, $node);
        if ($map_node) {
            $transformer->transform($image, $node);
        }

        if ($this->getProperty(self::PROPERTY_LIKE, $node)) {
            $image->enableLike();
        }

        if ($this->getProperty(self::PROPERTY_COMMENTS, $node)) {
            $image->enableComments();
        }

        return $instant_article;
    }
}
