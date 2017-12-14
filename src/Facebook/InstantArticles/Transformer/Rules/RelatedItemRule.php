<?hh // strict
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\RelatedItem;
use Facebook\InstantArticles\Elements\RelatedArticles;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;
use Facebook\InstantArticles\Elements\Element;

class RelatedItemRule extends ConfigurationSelectorRule
{
    const PROPERTY_SPONSORED = 'related.sponsored';
    const PROPERTY_URL = 'related.url';

    public function getContextClass(): vec<string>
    {
        return vec[RelatedArticles::getClassName()];
    }

    public static function create(): RelatedItemRule
    {
        return new RelatedItemRule();
    }

    public static function createFrom(array<string, mixed> $configuration): RelatedItemRule
    {
        $related_item_rule = self::create();
        $related_item_rule->withSelector(Type::mixedToString($configuration['selector']));

        $related_item_rule->withProperties(
            vec[
                self::PROPERTY_SPONSORED,
                self::PROPERTY_URL
            ],
            $configuration
        );

        return $related_item_rule;
    }

    public function apply(Transformer $transformer, Element $related_articles, \DOMNode $node): Element
    {
        invariant($related_articles instanceof RelatedArticles, 'Error, $related_articles is not RelatedArticles.');
        $related_item = RelatedItem::create();
        $related_articles->addRelated($related_item);

        $url = $this->getPropertyString(self::PROPERTY_URL, $node);
        if ($url !== null) {
            $related_item->withURL($url);
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_URL,
                    $related_articles,
                    $node,
                    $this
                )
            );
        }

        if ($this->getPropertyBoolean(self::PROPERTY_SPONSORED, $node)) {
            $related_item->enableSponsored();
        }

        return $related_articles;
    }
}
