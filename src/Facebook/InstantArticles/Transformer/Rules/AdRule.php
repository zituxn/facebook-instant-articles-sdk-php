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
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Elements\Ad;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;

class AdRule extends ConfigurationSelectorRule
{
    const PROPERTY_AD_URL = 'ad.url';
    const PROPERTY_AD_HEIGHT_URL = 'ad.height';
    const PROPERTY_AD_WIDTH_URL = 'ad.width';
    const PROPERTY_AD_EMBED_URL = 'ad.embed';

    public function getContextClass(): Vector<string>
    {
        return Vector { InstantArticle::getClassName() };
    }

    public static function create(): ConfigurationSelectorRule
    {
        return new AdRule();
    }

    public static function createFrom(Map $configuration): ConfigurationSelectorRule
    {
        $ad_rule = self::create();
        $ad_rule->withSelector(Type::mapGetString($configuration, 'selector'));

        $ad_rule->withProperties(
            Vector {
                self::PROPERTY_AD_URL,
                self::PROPERTY_AD_HEIGHT_URL,
                self::PROPERTY_AD_WIDTH_URL,
                self::PROPERTY_AD_EMBED_URL,
            },
            $configuration
        );

        return $ad_rule;
    }

    public function apply(Transformer $transformer, Element $instant_article, \DOMNode $node): Element
    {
        $ad = Ad::create();

        // Builds the ad
        $height = $this->getProperty(self::PROPERTY_AD_HEIGHT_URL, $node);
        if ($height) {
            $ad->withHeight($height);
        }

        $width = $this->getProperty(self::PROPERTY_AD_WIDTH_URL, $node);
        if ($width) {
            $ad->withWidth($width);
        }

        $url = $this->getProperty(self::PROPERTY_AD_URL, $node);
        if ($url) {
            $ad->withSource($url);
        }

        $embed_code = $this->getProperty(self::PROPERTY_AD_EMBED_URL, $node);
        if ($embed_code) {
            $ad->withHTML($embed_code);
        }

        if ($url || $embed_code) {
            Type::elementAsInstantArticle($instant_article)->addChild($ad);
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    'embed code or url',
                    $instant_article,
                    $node,
                    $this
                )
            );
        }

        return $instant_article;
    }
}
