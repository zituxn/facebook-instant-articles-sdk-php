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
use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Elements\H2;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class HeaderSubTitleRule extends ConfigurationSelectorRule
{
    public function getContextClass(): vec<string>
    {
        return vec[Header::getClassName()];
    }

    public static function create(): HeaderSubTitleRule
    {
        return new HeaderSubTitleRule();
    }

    public static function createFrom(array<string, mixed> $configuration): HeaderSubTitleRule
    {
        $headerSubTitleRule = self::create();
        $headerSubTitleRule->withSelector(Type::mixedToString($configuration['selector']));
        return $headerSubTitleRule;
    }

    public function apply(Transformer $transformer, Element $header, \DOMNode $h2): Element
    {
        invariant($header instanceof Header, 'Error, $header not Header');
        $subtitle = H2::create();
        $transformer->transform($subtitle, $h2);
        $header->withSubTitle($subtitle);
        return $header;
    }
}
