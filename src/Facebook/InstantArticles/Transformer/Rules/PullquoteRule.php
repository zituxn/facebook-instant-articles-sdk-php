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
use Facebook\InstantArticles\Elements\Pullquote;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class PullquoteRule extends ConfigurationSelectorRule
{
    public function getContextClass(): vec<string>
    {
        return vec[InstantArticle::getClassName()];
    }

    public static function create(): PullquoteRule
    {
        return new PullquoteRule();
    }

    public static function createFrom(dict<string, mixed> $configuration): PullquoteRule
    {
        $pullquoteRule = self::create();
        $pullquoteRule->withSelector(Type::mixedToString($configuration['selector']));
        return $pullquoteRule;
    }

    public function apply(Transformer $transformer, Element $instant_article, \DOMNode $element): Element
    {
        $pullquote = Pullquote::create();
        invariant($instant_article instanceof InstantArticle, 'Error, $instant_article is not InstantArticle');
        $instant_article->addChild($pullquote);
        $transformer->transform($pullquote, $element);
        return $instant_article;
    }
}
