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
use Facebook\InstantArticles\Elements\H1;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class HeaderTitleRule extends ConfigurationSelectorRule
{
    public function getContextClass(): vec<string>
    {
        return vec[Header::getClassName()];
    }

    public static function create(): HeaderTitleRule
    {
        return new HeaderTitleRule();
    }

    public static function createFrom(array<string, mixed> $configuration): HeaderTitleRule
    {
        $headerTitleRule = self::create();
        $headerTitleRule->withSelector(Type::mixedToString($configuration['selector']));
        return $headerTitleRule;
    }

    public function apply(Transformer $transformer, Element $header, \DOMNode $h1): Element
    {
        invariant($header instanceof Header, 'Error, $header is not Header');
        $title = H1::create();
        $transformer->transform($title, $h1);
        $header->withTitle($title);
        return $header;
    }
}
