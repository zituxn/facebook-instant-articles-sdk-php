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
use Facebook\InstantArticles\Elements\Analytics;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;
use Facebook\InstantArticles\Transformer\Transformer;

class AnalyticsRule extends ConfigurationSelectorRule
{
    const PROPERTY_TRACKER_URL = 'analytics.url';
    const PROPERTY_TRACKER_EMBED_URL = 'analytics.embed';

    public function getContextClass(): Vector<string>
    {
        return Vector { InstantArticle::getClassName() };
    }

    public static function create(): AnalyticsRule
    {
        return new AnalyticsRule();
    }

    public static function createFrom(array<string, mixed> $configuration): AnalyticsRule
    {
        $analytics_rule = self::create();
        $analytics_rule->withSelector(Type::mixedToString($configuration['selector']));

        $analytics_rule->withProperties(
            Vector {
                self::PROPERTY_TRACKER_URL,
                self::PROPERTY_TRACKER_EMBED_URL,
            },
            $configuration
        );

        return $analytics_rule;
    }

    public function apply(Transformer $transformer, Element $instant_article, \DOMNode $node): Element
    {
        $analytics = Analytics::create();

        // Builds the analytics
        $url = $this->getPropertyString(self::PROPERTY_TRACKER_URL, $node);
        if ($url !== null) {
            $analytics->withSource($url);
        }

        $embed_code = $this->getPropertyNode(self::PROPERTY_TRACKER_EMBED_URL, $node);
        if ($embed_code !== null) {
            $analytics->withHTML($embed_code);
        }

        if ($url !== null || $embed_code !== null) {
            invariant($instant_article instanceof InstantArticle, 'Error, $element is not a InstantArticle.');
            $instant_article->addChild($analytics);
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
