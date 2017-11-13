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
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class HeaderKickerRule extends ConfigurationSelectorRule
{
    public function getContextClass(): Vector<string>
    {
        return Vector { Header::getClassName() };
    }

    public static function create(): HeaderKickerRule
    {
        return new HeaderKickerRule();
    }

    public static function createFrom(Map $configuration): HeaderKickerRule
    {
        $kickerRule = self::create();
        $kickerRule->withSelector(Type::mapGetString($configuration, 'selector'));
        return $kickerRule;
    }

    public function apply(Transformer $transformer, Element $header, \DOMNode $h3): Element
    {
        invariant($header instanceof Header, 'Error, $header is not Header');
        $kicker = $transformer->transform(H3::create(), $h3);
        invariant($kicker instanceof H3, 'Error, $kicker is not H3.');
        $header->withKicker($kicker);
        return $header;
    }
}
