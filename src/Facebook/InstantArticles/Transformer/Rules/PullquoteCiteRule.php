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
use Facebook\InstantArticles\Elements\Cite;
use Facebook\InstantArticles\Elements\Pullquote;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class PullquoteCiteRule extends ConfigurationSelectorRule
{
    public function getContextClass(): Vector<string>
    {
        return Vector { Pullquote::getClassName() };
    }

    public static function create(): PullquoteCiteRule
    {
        return new PullquoteCiteRule();
    }

    public static function createFrom(array $configuration): PullquoteCiteRule
    {
        $cite_rule = self::create();
        $cite_rule->withSelector($configuration['selector']);

        return $cite_rule;
    }

    public function apply(Transformer $transformer, Element $pullquote, \DOMNode $node): Element
    {
        $cite = Cite::create();
        invariant($pullquote instanceof Pullquote, 'Error, $pullquote is not Pullquote');
        $pullquote->withAttribution($cite);
        $transformer->transform($cite, $node);

        return $pullquote;
    }
}
