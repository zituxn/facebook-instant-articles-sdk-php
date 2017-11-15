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

class XpathGetter extends AbstractGetter
{
    /**
     * @var string
     */
    protected ?string $attribute;

    public function createFrom(array<string, string> $properties): XpathGetter
    {
        if (isset($properties['selector'])) {
            $this->withSelector($properties['selector']);
        }
        if (isset($properties['attribute'])) {
            $this->withAttribute($properties['attribute']);
        }
        return $this;
    }

    /**
     * @param string $attribute
     *
     * @return $this
     */
    public function withAttribute(string $attribute): XpathGetter
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function get(\DOMNode $node): mixed
    {
        $domXPath = new \DOMXPath($node->ownerDocument);
        $elements = $domXPath->query($this->selector, $node);

        if (!empty($elements) && $elements->item(0)) {
            $element = $elements->item(0);
            if ($this->attribute) {
                return $element->getAttribute($this->attribute);
            }
            return $element->textContent;
        }
        return null;
    }
}
