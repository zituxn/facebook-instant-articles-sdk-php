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
use Facebook\InstantArticles\Elements\Footer;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class FooterRule extends ConfigurationSelectorRule
{
    public function getContextClass(): Vector<string>
    {
        return Vector { InstantArticle::getClassName() };
    }

    public static function create(): FooterRule
    {
        return new FooterRule();
    }

    public static function createFrom(Map $configuration): FooterRule
    {
        $footerRule = self::create();
        $footerRule->withSelector(Type::mapGetString($configuration, 'selector'));

        return $footerRule;
    }

    public function apply(Transformer $transformer, Element $instant_article, \DOMNode $node): Element
    {
        $footer = Footer::create();
        invariant($instant_article instanceof InstantArticle, 'Error, $element is not a InstantArticle.');
        $instant_article->withFooter($footer);
        $transformer->transform($footer, $node);
        return $instant_article;
    }
}
