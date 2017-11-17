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
use Facebook\InstantArticles\Elements\Anchor;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class AnchorRule extends ConfigurationSelectorRule
{
    const PROPERTY_ANCHOR_HREF = 'anchor.href';
    const PROPERTY_ANCHOR_REL = 'anchor.rel';

    public static function create(): AnchorRule
    {
        return new AnchorRule();
    }

    public function getContextClass(): Vector<string>
    {
        return Vector { TextContainer::getClassName() };
    }

    public static function createFrom(array $configuration): AnchorRule
    {
        $anchor_rule = self::create();

        $anchor_rule->withSelector($configuration['selector']);
        //$properties = $configuration['properties'];
        $anchor_rule->withProperties(
            Vector {
                self::PROPERTY_ANCHOR_HREF,
                self::PROPERTY_ANCHOR_REL,
            },
            $configuration
        );

        return $anchor_rule;
    }

    public function apply(Transformer $transformer, Element $text_container, \DOMNode $element): Element
    {
        $anchor = Anchor::create();

        $url = $this->getPropertyString(self::PROPERTY_ANCHOR_HREF, $element);
        $rel = $this->getPropertyString(self::PROPERTY_ANCHOR_REL, $element);

        if ($url) {
            $anchor->withHref($url);
        }
        if ($rel) {
            $anchor->withRel($rel);
        }
        invariant($text_container instanceof TextContainer, 'Error, $text_container is not a TextContainer.');
        $text_container->appendText($anchor);
        $transformer->transform($anchor, $element);

        return $text_container;
    }
}
