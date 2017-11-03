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
use Facebook\InstantArticles\Elements\H2;

class HeaderSubTitleRule extends ConfigurationSelectorRule
{
    public function getContextClass(): Vector<string>
    {
        return Vector { Header::getClassName() };
    }

    public static function create(): HeaderSubTitleRule
    {
        return new HeaderSubTitleRule();
    }

    public static function createFrom(Map $configuration): HeaderSubTitleRule
    {
        $headerSubTitleRule = self::create();
        $headerSubTitleRule->withSelector(Type::mapGetString($configuration, 'selector'));
        return $headerSubTitleRule;
    }

    public function apply(Transformer $transformer, Element $header, \DOMNode $h2): Element
    {
        invariant($header instanceof Header, 'Error, $header not Header');
        $header->withSubTitle($transformer->transform(H2::create(), $h2));
        return $header;
    }
}
