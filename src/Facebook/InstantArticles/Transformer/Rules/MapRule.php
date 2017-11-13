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
use Facebook\InstantArticles\Elements\MapElement;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class MapRule extends ConfigurationSelectorRule
{
    public function getContextClass(): Vector<string>
    {
        return Vector { InstantArticle::getClassName() };
    }

    public static function create(): MapRule
    {
        return new MapRule();
    }

    public static function createFrom(Map $configuration): MapRule
    {
        $map_rule = self::create();
        $map_rule->withSelector(Type::mapGetString($configuration, 'selector'));
        return $map_rule;
    }

    public function apply(Transformer $transformer, Element $instant_article, \DOMNode $node): Element
    {
        $map = MapElement::create();
        invariant($instant_article instanceof InstantArticle, 'Error, $instant_article is not InstantArticle');
        $instant_article->addChild($map);
        $transformer->transform($map, $node);

        return $instant_article;
    }
}
