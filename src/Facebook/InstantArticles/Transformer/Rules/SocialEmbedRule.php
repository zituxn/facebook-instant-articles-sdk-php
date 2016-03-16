<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\SocialEmbed;
use Facebook\InstantArticles\Elements\Caption;
use Facebook\InstantArticles\Elements\InstantArticle;

class SocialEmbedRule extends ConfigurationSelectorRule
{
    const PROPERTY_IFRAME = 'socialembed.iframe';
    const PROPERTY_URL = 'socialembed.url';
    const PROPERTY_CAPTION = 'socialembed.caption';

    public function getContextClass()
    {
        return InstantArticle::class;
    }

    public static function create()
    {
        return new SocialEmbedRule();
    }

    public static function createFrom($configuration)
    {
        $social_embed_rule = self::create();
        $social_embed_rule->withSelector($configuration['selector']);

        $social_embed_rule->withProperties(
            array(
                self::PROPERTY_IFRAME,
                self::PROPERTY_URL,
                self::PROPERTY_CAPTION
            ),
            $configuration
        );

        return $social_embed_rule;
    }

    public function apply($transformer, $instant_article, $node)
    {
        $social_embed = SocialEmbed::create();

        // Builds the image
        $iframe = $this->getProperty(self::PROPERTY_IFRAME, $node);
        $url = $this->getProperty(self::PROPERTY_URL, $node);
        if ($iframe) {
            $social_embed->withHTML($iframe);
        }
        if ($url) {
            $social_embed->withSource($url);
        }
        if ($iframe || $url) {
            $instant_article->addChild($social_embed);
        } else {
            throw new \InvalidArgumentException('Invalid selector for '.self::PROPERTY_IFRAME);
        }

        $caption_node = $this->getProperty(self::PROPERTY_CAPTION, $node);
        if ($caption_node) {
            $transformer->transform($social_embed, $node);
        }

        return $instant_article;
    }
}
