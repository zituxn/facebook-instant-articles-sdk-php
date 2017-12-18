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
use Facebook\InstantArticles\Elements\Footer;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class FooterRelatedArticlesRule extends ConfigurationSelectorRule
{
    public function getContextClass(): vec<string>
    {
        return vec[Footer::getClassName()];
    }

    public static function create(): FooterRelatedArticlesRule
    {
        return new FooterRelatedArticlesRule();
    }

    public static function createFrom(dict<string, mixed> $configuration): FooterRelatedArticlesRule
    {
        $related_articles_rule = self::create();
        $related_articles_rule->withSelector(Type::mixedToString($configuration['selector']));

        return $related_articles_rule;
    }

    public function apply(Transformer $transformer, Element $footer, \DOMNode $node): Element
    {
        $related_articles = RelatedArticles::create();
        invariant($footer instanceof Footer, 'Error, $footer is not a Footer.');
        $footer->withRelatedArticles($related_articles);

        $transformer->transform($related_articles, $node);

        return $footer;
    }
}
