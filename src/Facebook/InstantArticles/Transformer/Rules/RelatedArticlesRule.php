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
use Facebook\InstantArticles\Elements\RelatedArticles;
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class RelatedArticlesRule extends ConfigurationSelectorRule
{
    const PROPERTY_TITLE = 'related.title';

    public function getContextClass(): vec<string>
    {
        return vec[InstantArticle::getClassName()];
    }

    public static function create(): RelatedArticlesRule
    {
        return new RelatedArticlesRule();
    }

    public static function createFrom(array<string, mixed> $configuration): RelatedArticlesRule
    {
        $related_articles_rule = self::create();
        $related_articles_rule->withSelector(Type::mixedToString($configuration['selector']));

        $related_articles_rule->withProperties(
            vec[
                self::PROPERTY_TITLE,
            ],
            $configuration
        );

        return $related_articles_rule;
    }

    public function apply(Transformer $transformer, Element $instant_article, \DOMNode $node): Element
    {
        invariant($instant_article instanceof InstantArticle, 'Error, $instant_article is not InstantArticle');
        $related_articles = RelatedArticles::create();

        $title = $this->getPropertyString(self::PROPERTY_TITLE, $node);
        if ($title !== null) {
            $related_articles->withTitle($title);
        }
        $instant_article->addChild($related_articles);

        $transformer->transform($related_articles, $node);

        return $instant_article;
    }
}
