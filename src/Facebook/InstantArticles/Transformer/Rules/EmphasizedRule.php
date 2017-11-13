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
use Facebook\InstantArticles\Elements\TextContainer;
use Facebook\InstantArticles\Elements\Emphasized;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class EmphasizedRule extends ConfigurationSelectorRule
{
    public function getContextClass(): Vector<string>
    {
        return Vector { TextContainer::getClassName() };
    }

    public static function create(): EmphasizedRule
    {
        return new EmphasizedRule();
    }

    public static function createFrom(Map $configuration): EmphasizedRule
    {
        $emphasizedRule = self::create();
        $emphasizedRule->withSelector(Type::mapGetString($configuration, 'selector'));
        return $emphasizedRule;
    }

    public function apply(Transformer $transformer, Element $text_container, \DOMNode $element): Element
    {
        $emphasized = Emphasized::create();
        invariant($text_container instanceof TextContainer, 'Error, $text_container is not a TextContainer.');
        $text_container->appendText($emphasized);
        $transformer->transform($emphasized, $element);
        return $text_container;
    }
}
