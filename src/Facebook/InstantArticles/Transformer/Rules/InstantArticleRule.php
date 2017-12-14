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
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class InstantArticleRule extends ConfigurationSelectorRule
{
    const PROPERTY_CANONICAL = 'article.canonical';
    const PROPERTY_CHARSET = 'article.charset';
    const PROPERTY_MARKUP_VERSION = 'article.markup.version';
    const PROPERTY_AUTO_AD_PLACEMENT = 'article.auto.ad';
    const PROPERTY_STYLE = 'article.style';

    public function getContextClass(): vec<string>
    {
        return vec[InstantArticle::getClassName()];
    }

    public static function create(): InstantArticleRule
    {
        return new InstantArticleRule();
    }

    public static function createFrom(array<string, mixed> $configuration): InstantArticleRule
    {
        $canonical_rule = self::create();
        $canonical_rule->withSelector(Type::mixedToString($configuration['selector']));

        $canonical_rule->withProperties(
            vec[
                self::PROPERTY_CANONICAL,
                self::PROPERTY_CHARSET,
                self::PROPERTY_MARKUP_VERSION,
                self::PROPERTY_AUTO_AD_PLACEMENT,
                self::PROPERTY_STYLE,
            ],
            $configuration
        );

        return $canonical_rule;
    }

    public function apply(Transformer $transformer, Element $instant_article, \DOMNode $node): Element
    {
        invariant($instant_article instanceof InstantArticle, 'Error, $instant_article is not InstantArticle');
        // Builds the image
        $url = $this->getPropertyString(self::PROPERTY_CANONICAL, $node);
        if ($url !== null) {
            $instant_article->withCanonicalUrl($url);
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_CANONICAL,
                    $instant_article,
                    $node,
                    $this
                )
            );
        }

        $charset = $this->getPropertyString(self::PROPERTY_CHARSET, $node);
        if ($charset !== null) {
            $instant_article->withCharset($charset);
        }

        $markup_version = $this->getPropertyString(self::PROPERTY_MARKUP_VERSION, $node);
        if ($markup_version !== null) {
            //TODO Validate if the markup is valid with this code
        }

        $auto_ad_placement = $this->getPropertyString(self::PROPERTY_AUTO_AD_PLACEMENT, $node);
        if ($auto_ad_placement === 'false') {
            $instant_article->disableAutomaticAdPlacement();
        } else {
            $instant_article->enableAutomaticAdPlacement();
            $pairs = explode(' ', $auto_ad_placement !== null ? $auto_ad_placement : '', 2);
            if (count($pairs) === 2) {
                list($name, $value) = explode('=', $pairs[1], 2);
                $instant_article->withAdDensity($value);
            }
        }

        $style = $this->getPropertyString(self::PROPERTY_STYLE, $node);
        if ($style !== null) {
            $instant_article->withStyle($style);
        }

        return $instant_article;
    }
}
