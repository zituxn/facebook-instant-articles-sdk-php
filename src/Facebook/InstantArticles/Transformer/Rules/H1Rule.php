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
use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Elements\Caption;
use Facebook\InstantArticles\Elements\H1;
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class H1Rule extends ConfigurationSelectorRule
{
    public function getContextClass(): vec<string>
    {
        return vec[
            Header::getClassName(),
            Caption::getClassName(),
            InstantArticle::getClassName()
        ];
    }

    public static function create(): H1Rule
    {
        return new H1Rule();
    }

    public static function createFrom(array<string, mixed> $configuration): H1Rule
    {
        $h1_rule = self::create();
        $h1_rule->withSelector(Type::mixedToString($configuration['selector']));

        $h1_rule->withProperties(
            vec[
                Caption::POSITION_BELOW,
                Caption::POSITION_CENTER,
                Caption::POSITION_ABOVE,

                Caption::ALIGN_LEFT,
                Caption::ALIGN_CENTER,
                Caption::ALIGN_RIGHT,

                Caption::SIZE_SMALL,
                Caption::SIZE_MEDIUM,
                Caption::SIZE_LARGE,
                Caption::SIZE_XLARGE,
            ],
            $configuration
        );

        return $h1_rule;
    }

    public function apply(Transformer $transformer, Element $context_element, \DOMNode $node): Element
    {
        $h1 = H1::create();
        if ($context_element instanceof Header) {
            $context_element->withTitle($h1);
        } elseif ($context_element instanceof Caption) {
            $context_element->withTitle($h1);
        } elseif ($context_element instanceof InstantArticle) {
            $context_element->addChild($h1);
        }

        if ($this->getProperty(Caption::POSITION_BELOW, $node)) {
            $h1->withPosition(Caption::POSITION_BELOW);
        }
        if ($this->getProperty(Caption::POSITION_CENTER, $node)) {
            $h1->withPosition(Caption::POSITION_CENTER);
        }
        if ($this->getProperty(Caption::POSITION_ABOVE, $node)) {
            $h1->withPosition(Caption::POSITION_ABOVE);
        }

        if ($this->getProperty(Caption::ALIGN_LEFT, $node)) {
            $h1->withTextAlignment(Caption::ALIGN_LEFT);
        }
        if ($this->getProperty(Caption::ALIGN_CENTER, $node)) {
            $h1->withTextAlignment(Caption::ALIGN_CENTER);
        }
        if ($this->getProperty(Caption::ALIGN_RIGHT, $node)) {
            $h1->withTextAlignment(Caption::ALIGN_RIGHT);
        }

        $transformer->transform($h1, $node);
        return $context_element;
    }
}
