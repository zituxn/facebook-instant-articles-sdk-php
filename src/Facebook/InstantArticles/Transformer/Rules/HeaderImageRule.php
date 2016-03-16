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
use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Transformer\Getters\GetterFactory;
use Facebook\InstantArticles\Transformer\Getters\StringGetter;
use Facebook\InstantArticles\Transformer\Getters\ChildrenGetter;

class HeaderImageRule extends ConfigurationSelectorRule
{
    const PROPERTY_IMAGE_URL = 'image.url';
    const PROPERTY_CAPTION = 'image.caption';

    public function __construct()
    {
    }

    public function getContextClass()
    {
        return Header::class;
    }

    public static function create()
    {
        return new HeaderImageRule();
    }

    public static function createFrom($configuration)
    {
        $image_rule = self::create();
        $image_rule->withSelector($configuration['selector']);

        $image_rule->withProperties(
            array(
                self::PROPERTY_IMAGE_URL,
                self::PROPERTY_CAPTION
            ),
            $configuration
        );

        return $image_rule;
    }

    public function apply($transformer, $header, $node)
    {
        $image = Image::create();

        // Builds the image
        $url = $this->getProperty(self::PROPERTY_IMAGE_URL, $node);
        if ($url) {
            $image->withURL($url);
            $header->withCover($image);
        } else {
            throw new \InvalidArgumentException('Invalid selector for '.self::PROPERTY_IMAGE_URL);
        }

        $caption_node = $this->getProperty(self::PROPERTY_CAPTION, $node);
        if ($caption_node) {
            $transformer->transform($image, $node);
        }

        return $header;
    }
}
