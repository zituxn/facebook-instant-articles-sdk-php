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

class ExistsGetter extends AbstractGetter
{
    public function createFrom(array<string, string> $properties): ExistsGetter
    {
        if (isset($properties['selector'])) {
            $this->withSelector($properties['selector']);
        }
        if (isset($properties['attribute'])) {
            $this->withAttribute($properties['attribute']);
        }
        return $this;
    }

    public function get(\DOMNode $node): mixed
    {
        $elements = $this->findAll($node, $this->selector);
        if (!empty($elements) && $elements->item(0)) {
            if (!$this->attribute) {
                return true;
            }
            return $elements->item(0)->hasAttribute($this->attribute);
        }
        return false;
    }
}
