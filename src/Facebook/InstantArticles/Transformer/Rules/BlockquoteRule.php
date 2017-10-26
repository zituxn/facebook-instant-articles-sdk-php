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
use Facebook\InstantArticles\Elements\Blockquote;

class BlockquoteRule extends ConfigurationSelectorRule
{
    public function getContextClass(): Vector<string>
    {
        return Vector { InstantArticle::getClassName() };
    }

    public static function create(): BlockquoteRule
    {
        return new BlockquoteRule();
    }

    public static function createFrom(Map $configuration): BlockquoteRule
    {
        $blockquoteRule = BlockquoteRule::create();
        $blockquoteRule->withSelector(Type::mapGetString($configuration, 'selector'));

        return $blockquoteRule;
    }

    public function apply(Transformer $transformer, Element $instant_article, \DOMNode $element): Element
    {
        $blockquote = Blockquote::create();
        Type::elementAsInstantArticle($instant_article)->addChild($blockquote);
        $transformer->transform($blockquote, $element);
        return $instant_article;
    }
}
