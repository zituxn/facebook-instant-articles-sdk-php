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
use Facebook\InstantArticles\Elements\Bold;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class BoldRule extends ConfigurationSelectorRule
{
    public function getContextClass(): Vector<string>
    {
        return Vector { TextContainer::getClassName() };
    }

    public static function create(): BoldRule
    {
        return new BoldRule();
    }

    public static function createFrom(array<string, mixed> $configuration): BoldRule
    {
        $boldRule = BoldRule::create();
        $boldRule->withSelector(Type::mixedToString($configuration['selector']));
        return $boldRule;
    }

    public function apply(Transformer $transformer, Element $text_container, \DOMNode $element): Element
    {
        $bold = Bold::create();
        invariant($text_container instanceof TextContainer, 'Error, $text_container is not a TextContainer.');
        $text_container->appendText($bold);
        $transformer->transform($bold, $element);
        return $text_container;
    }
}
