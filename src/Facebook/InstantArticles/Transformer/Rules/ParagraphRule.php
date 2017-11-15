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
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Elements\Paragraph;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class ParagraphRule extends ConfigurationSelectorRule
{
    public function getContextClass(): Vector<string>
    {
        return Vector { InstantArticle::getClassName() };
    }

    public static function create(): ParagraphRule
    {
        return new ParagraphRule();
    }

    public static function createFrom(array $configuration): ParagraphRule
    {
        $paragraphRule = self::create();
        $paragraphRule->withSelector($configuration['selector']);
        return $paragraphRule;
    }

    public function apply(Transformer $transformer, Element $instant_article, \DOMNode $element): Element
    {
        $p = Paragraph::create();
        invariant($instant_article instanceof InstantArticle, 'Error, $instant_article is not InstantArticle');
        $instant_article->addChild($p);
        $transformer->transform($p, $element);

        return $instant_article;
    }
}
