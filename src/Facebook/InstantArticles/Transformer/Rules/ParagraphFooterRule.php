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
use Facebook\InstantArticles\Elements\Paragraph;
use Facebook\InstantArticles\Elements\Footer;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class ParagraphFooterRule extends ConfigurationSelectorRule
{
    public function getContextClass(): Vector<string>
    {
        return Vector { Footer::getClassName() };
    }

    public static function create(): ParagraphFooterRule
    {
        return new ParagraphFooterRule();
    }

    public static function createFrom(array<string, mixed> $configuration): ParagraphFooterRule
    {
        $paragraphFooterRule = self::create();
        $paragraphFooterRule->withSelector(Type::mixedToString($configuration['selector']));
        return $paragraphFooterRule;
    }

    public function apply(Transformer $transformer, Element $footer, \DOMNode $element): Element
    {
        $p = Paragraph::create();
        invariant($footer instanceof Footer, 'Error, $footer is not Footer');
        $footer->addCredit($p);
        $transformer->transform($p, $element);
        return $footer;
    }
}
