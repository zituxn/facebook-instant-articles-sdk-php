<?hh // strict
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Getters;

use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;

class NextSiblingElementGetter extends AbstractGetter
{
    protected ?string $siblingSelector;

    /**
     * @param string $siblingSelector
     *
     * @return $this
     */
    public function withSiblingSelector(string $siblingSelector): this
    {
        $this->siblingSelector = $siblingSelector;

        return $this;
    }

    public function createFrom(dict<string, mixed> $properties): this
    {
        if (array_key_exists('selector', $properties)) {
            $this->withSelector(Type::mixedToString($properties['selector']));
        }
        if (array_key_exists('attribute', $properties)) {
            $this->withAttribute(Type::mixedToString($properties['attribute']));
        }
        if (array_key_exists('sibling.selector', $properties)) {
            $this->withSiblingSelector(Type::mixedToString($properties['sibling.selector']));
        }

        return $this;
    }

    public function get(\DOMNode $node): mixed
    {
        $elements = $this->findAll($node, $this->selector);
        if ($elements !== null && $elements->length > 0 && $elements->item(0)) {
            $element = $elements->item(0);
            do {
                $element = $element->nextSibling;
            } while ($element !== null && !($element instanceof \DOMElement));

            if ($element && $element instanceof \DOMElement) {
                if ($this->siblingSelector !== null) {
                    $siblings = $this->findAll($element, $this->siblingSelector);
                    if ($siblings !== null && $siblings->length > 0 && $siblings->item(0) !== null) {
                        $siblingElement = $siblings->item(0);
                    } else {
                        // Returns null because sibling content doesn't match
                        return null;
                    }
                } else {
                    $siblingElement = $element;
                }
                Transformer::markAsProcessed($siblingElement);
                return Transformer::cloneNode($siblingElement);
            }
        }
        return null;
    }
}
