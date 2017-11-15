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

class JSONGetter extends AbstractGetter
{
    public function createFrom(array<string, string> $properties): JSONGetter
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
        $content = "";

        $elements = $this->findAll($node, $this->selector);
        if (!empty($elements) && $elements->item(0)) {
            $element = $elements->item(0);
            if ($this->attribute) {
                $content = $element->getAttribute($this->attribute);
            } else {
                $content = $element->textContent;
            }
        }

        if (!Type::isTextEmpty($content)) {
            return json_decode($content, true);
        }
        return null;
    }
}
