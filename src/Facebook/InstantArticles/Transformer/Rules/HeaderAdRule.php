<?hh // strict
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Element;
use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Elements\Ad;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class HeaderAdRule extends ConfigurationSelectorRule
{
    const PROPERTY_AD_URL = 'ad.url';
    const PROPERTY_AD_HEIGHT_URL = 'ad.height';
    const PROPERTY_AD_WIDTH_URL = 'ad.width';
    const PROPERTY_AD_EMBED_URL = 'ad.embed';

    public function getContextClass(): vec<string>
    {
        return vec[Header::getClassName()];
    }

    public static function create(): HeaderAdRule
    {
        return new HeaderAdRule();
    }

    public static function createFrom(dict<string, mixed> $configuration): HeaderAdRule
    {
        $ad_rule = self::create();
        $ad_rule->withSelector(Type::mixedToString($configuration['selector']));

        $ad_rule->withProperties(
            vec[
                self::PROPERTY_AD_URL,
                self::PROPERTY_AD_HEIGHT_URL,
                self::PROPERTY_AD_WIDTH_URL,
                self::PROPERTY_AD_EMBED_URL,
            ],
            $configuration
        );

        return $ad_rule;
    }

    public function apply(Transformer $transformer, Element $header, \DOMNode $node): Element
    {
        invariant($header instanceof Header, 'Error, $header is not Header');
        $ad = Ad::create();

        // Builds the ad
        $height = $this->getPropertyInt(self::PROPERTY_AD_HEIGHT_URL, $node);
        if ($height !== null) {
            $ad->withHeight($height);
        }

        $width = $this->getPropertyInt(self::PROPERTY_AD_WIDTH_URL, $node);
        if ($width !== null) {
            $ad->withWidth($width);
        }

        $url = $this->getPropertyString(self::PROPERTY_AD_URL, $node);
        if ($url !== null) {
            $ad->withSource($url);
        }

        $embed_code = $this->getPropertyNode(self::PROPERTY_AD_EMBED_URL, $node);
        if ($embed_code !== null) {
            $ad->withHTML(Transformer::cloneNode($embed_code));
        }

        if ($url !== null || $embed_code !== null) {
            $header->addAd($ad);
        }

        return $header;
    }
}
