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
use Facebook\InstantArticles\Elements\Footer;
use Facebook\InstantArticles\Elements\Small;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class FooterSmallRule extends ConfigurationSelectorRule
{
    public function getContextClass(): Vector<string>
    {
        return Vector { Footer::getClassName() };
    }

    public static function create(): FooterSmallRule
    {
        return new FooterSmallRule();
    }

    public static function createFrom(array<string, mixed> $configuration): FooterSmallRule
    {
        $footerRule = self::create();
        $footerRule->withSelector(Type::mixedToString($configuration['selector']));
        return $footerRule;
    }

    public function apply(Transformer $transformer, Element $footer, \DOMNode $element): Element
    {
        $small = Small::create();
        invariant($footer instanceof Footer, 'Error, $footer is not Footer.');
        $footer->withCopyright($small);
        $transformer->transform($small, $element);
        return $footer;
    }
}
