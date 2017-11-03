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

class TextNodeRule extends ConfigurationSelectorRule
{
    public function getContextClass(): Vector<string>
    {
        return Vector { TextContainer::getClassName() };
    }

    public static function create(): TextNodeRule
    {
        return new TextNodeRule();
    }

    public static function createFrom(Map $configuration): TextNodeRule
    {
        return self::create();
    }

    public function matchesNode(\DOMNode $node): bool
    {
        if ($node->nodeName === '#text') {
            return true;
        }
        return false;
    }

    public function apply(Transformer $transformer, Element $text_container, \DOMNode $text): Element
    {
        invariant($text_container instanceof TextContainer, 'Error, $text_container is not TextContainer');
        $text_container->appendText($text->textContent);
        return $text_container;
    }
}
