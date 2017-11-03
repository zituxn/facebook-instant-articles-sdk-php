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
use Facebook\InstantArticles\Elements\Italic;

class ItalicRule extends ConfigurationSelectorRule
{
    public static function create(): ItalicRule
    {
        return new ItalicRule();
    }

    public static function createFrom(Map $configuration): ItalicRule
    {
        $italicRule = self::create();
        $italicRule->withSelector(Type::mapGetString($configuration, 'selector'));
        return $italicRule;
    }

    public function getContextClass(): Vector<string>
    {
        return Vector { TextContainer::getClassName() };
    }

    public function apply(Transformer $transformer, Element $text_container, \DOMNode $element): Element
    {
        $bold = Italic::create();
        invariant($text_container instanceof TextContainer, 'Error, $text_container is not TextContainer.');
        $text_container->appendText($bold);
        $transformer->transform($bold, $element);
        return $text_container;
    }
}
