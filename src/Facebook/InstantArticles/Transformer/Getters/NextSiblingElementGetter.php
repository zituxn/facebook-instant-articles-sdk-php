<?hh
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
    public function withSiblingSelector(string $siblingSelector): NextSiblingElementGetter
    {
        $this->siblingSelector = $siblingSelector;

        return $this;
    }

    public function createFrom(array<string, string> $properties): NextSiblingElementGetter
    {
        if (isset($properties['selector'])) {
            $this->withSelector($properties['selector']);
        }
        if (isset($properties['attribute'])) {
            $this->withAttribute($properties['attribute']);
        }
        if (isset($properties['sibling.selector'])) {
            $this->withSiblingSelector($properties['sibling.selector']);
        }

        return $this;
    }

    public function get(\DOMNode $node): mixed
    {
        $elements = $this->findAll($node, $this->selector);
        if (!empty($elements) && $elements->item(0)) {
            $element = $elements->item(0);
            do {
                $element = $element->nextSibling;
            } while ($element !== null && !($element instanceof \DOMNode));

            if ($element && $element instanceof \DOMNode) {
                if ($this->siblingSelector) {
                    $siblings = $this->findAll($element, $this->siblingSelector);
                    if (!empty($siblings) && $siblings->item(0)) {
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
