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
use Facebook\InstantArticles\Elements\ListElement;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class ListElementRule extends ConfigurationSelectorRule
{
    public function getContextClass(): vec<string>
    {
        return vec[InstantArticle::getClassName()];
    }

    public static function create(): ListElementRule
    {
        return new ListElementRule();
    }

    public static function createFrom(dict<string, mixed> $configuration): ListElementRule
    {
        $listElementRule = self::create();
        $listElementRule->withSelector(Type::mixedToString($configuration['selector']));
        return $listElementRule;
    }

    public function apply(Transformer $transformer, Element $instant_article, \DOMNode $element): Element
    {
        $list =
            $element->nodeName === 'ol' ?
                ListElement::createOrdered() :
                ListElement::createUnordered();
        invariant($instant_article instanceof InstantArticle, 'Error, $instant_article is not InstantArticle');
        $instant_article->addChild($list);
        $transformer->transform($list, $element);
        return $instant_article;
    }
}
