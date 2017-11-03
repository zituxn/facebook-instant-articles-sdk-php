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
use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Elements\H1;

class HeaderTitleRule extends ConfigurationSelectorRule
{
    public function getContextClass(): Vector<string>
    {
        return Vector { Header::getClassName() };
    }

    public static function create(): HeaderTitleRule
    {
        return new HeaderTitleRule();
    }

    public static function createFrom(Map $configuration): HeaderTitleRule
    {
        $headerTitleRule = self::create();
        $headerTitleRule->withSelector(Type::getMapString($configuration, 'selector'));
        return $headerTitleRule;
    }

    public function apply(Transformer $transformer, Element $header, \DOMNode $h1): Element
    {
        invariant($header instanceof Header, 'Error, $header not Header');
        $header->withTitle($transformer->transform(H1::create(), $h1));
        return $header;
    }
}
