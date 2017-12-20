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
use Facebook\InstantArticles\Elements\Slideshow;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class SlideshowRule extends ConfigurationSelectorRule
{
    public function getContextClass(): vec<string>
    {
        return vec[InstantArticle::getClassName()];
    }

    public static function create(): SlideshowRule
    {
        return new SlideshowRule();
    }

    public static function createFrom(dict<string, mixed> $configuration): SlideshowRule
    {
        $slideshow_rule = self::create();
        $slideshow_rule->withSelector(Type::mixedToString($configuration['selector']));

        return $slideshow_rule;
    }

    public function apply(Transformer $transformer, Element $instant_article, \DOMNode $node): Element
    {
        // Builds the slideshow
        $slideshow = Slideshow::create();
        invariant($instant_article instanceof InstantArticle, 'Error, $instant_article is not InstantArticle');
        $instant_article->addChild($slideshow);

        $transformer->transform($slideshow, $node);

        return $instant_article;
    }
}
