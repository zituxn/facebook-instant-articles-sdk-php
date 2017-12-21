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

class XpathGetter extends AbstractGetter
{
    /**
     * @var string
     */
    protected ?string $attribute;

    public function createFrom(dict<string, mixed> $properties): this
    {
        if (array_key_exists('selector', $properties)) {
            $this->withSelector(Type::mixedToString($properties['selector']));
        }
        if (array_key_exists('attribute', $properties)) {
            $this->withAttribute(Type::mixedToString($properties['attribute']));
        }
        return $this;
    }

    /**
     * @param string $attribute
     *
     * @return $this
     */
    public function withAttribute(string $attribute): this
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function get(\DOMNode $node): mixed
    {
        $domXPath = new \DOMXPath($node->ownerDocument);
        $elements = $domXPath->query($this->selector, $node);

        if ($elements !== null && $elements->length > 0 && $elements->item(0) !== null) {
            $element = $elements->item(0);
            if ($this->attribute !== null) {
                return $element->getAttribute($this->attribute);
            }
            return $element->textContent;
        }
        return null;
    }
}
