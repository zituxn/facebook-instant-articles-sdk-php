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
use Facebook\InstantArticles\Elements\TextContainer;
use Facebook\InstantArticles\Elements\LineBreak;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class LineBreakRule extends ConfigurationSelectorRule
{
    public function getContextClass(): vec<string>
    {
        return vec[TextContainer::getClassName()];
    }

    public static function create(): LineBreakRule
    {
        return new LineBreakRule();
    }

    public static function createFrom(array<string, mixed> $configuration): LineBreakRule
    {
        $lineBreakRule = self::create();
        $lineBreakRule->withSelector(Type::mixedToString($configuration['selector']));
        return $lineBreakRule;
    }

    public function apply(Transformer $transformer, Element $text_container, \DOMNode $element): Element
    {
        $line_break = LineBreak::create();
        invariant($text_container instanceof TextContainer, 'Error, $text_container is not TextContainer.');
        $text_container->appendText($line_break);
        return $text_container;
    }
}
