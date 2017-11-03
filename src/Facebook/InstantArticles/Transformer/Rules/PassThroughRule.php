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
use Facebook\InstantArticles\Elements\Element;

class PassThroughRule extends ConfigurationSelectorRule
{
    public function getContextClass(): Vector<string>
    {
        return Vector { Element::getClassName() };
    }

    public static function create(): PassThroughRule
    {
        return new PassThroughRule();
    }

    public static function createFrom(Map $configuration): PassThroughRule
    {
        $passThroughRule = self::create();
        $passThroughRule->withSelector(Type::mapGetString($configuration, 'selector'));
        return $passThroughRule;
    }

    public function apply(Transformer $transformer, Element $context, \DOMNode $node): Element
    {
        $transformer->transform($context, $node);
        return $context;
    }
}
