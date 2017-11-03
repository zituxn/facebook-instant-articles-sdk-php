<?hh //decl
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Element;
use Facebook\InstantArticles\Elements\RelatedItem;
use Facebook\InstantArticles\Elements\RelatedArticles;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;

class RelatedItemRule extends ConfigurationSelectorRule
{
    const PROPERTY_SPONSORED = 'related.sponsored';
    const PROPERTY_URL = 'related.url';

    public function getContextClass(): Vector<string>
    {
        return Vector { RelatedArticles::getClassName() };
    }

    public static function create(): RelatedItemRule
    {
        return new RelatedItemRule();
    }

    public static function createFrom(Map $configuration): RelatedItemRule
    {
        $related_item_rule = self::create();
        $related_item_rule->withSelector($configuration['selector']);

        $related_item_rule->withProperties(
            [
                self::PROPERTY_SPONSORED,
                self::PROPERTY_URL
            ],
            $configuration
        );

        return $related_item_rule;
    }

    public function apply(Transformer $transformer, \Facebook\InstantArticles\Elements\Element $related_articles, \DOMNode $node): \Facebook\InstantArticles\Elements\Element
    {
        $related_item = RelatedItem::create();
        $related_articles->addRelated($related_item);

        $url = $this->getPropertyString(self::PROPERTY_URL, $node);
        if ($url) {
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

        if ($this->getProperty(self::PROPERTY_SPONSORED, $node)) {
            $related_item->enableSponsored();
        }

        return $related_articles;
    }
}
