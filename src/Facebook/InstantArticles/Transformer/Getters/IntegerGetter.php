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

class IntegerGetter extends AbstractGetter
{
    public function createFrom(Map<string, string> $properties): IntegerGetter
    {
        if (isset($properties['selector'])) {
            $this->withSelector($properties['selector']);
        }
        if (isset($properties['attribute'])) {
            $this->withAttribute($properties['attribute']);
        }
        return $this;
    }

    public function get(\DOMNode $node): ?int
    {
        $elements = $this->findAll($node, $this->selector);
        if (!empty($elements) && $elements->item(0)) {
            $element = $elements->item(0);
            if ($this->attribute) {
                return intval($element->getAttribute($this->attribute));
            }
            return intval($element->textContent);
        }
        return null;
    }
}
