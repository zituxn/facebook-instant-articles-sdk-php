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

class StringGetter extends AbstractGetter
{
    /**
     * @var string
     */
    protected string $prefix = "";

    /**
     * @var string
     */
    protected string $suffix = "";

    public function createFrom(dict<string, mixed> $properties): this
    {
        if (array_key_exists('selector', $properties)) {
            $this->withSelector(Type::mixedToString($properties['selector']));
        }
        if (array_key_exists('attribute', $properties)) {
            $this->withAttribute(Type::mixedToString($properties['attribute']));
        }
        if (array_key_exists('prefix', $properties)) {
            $this->withPrefix(Type::mixedToString($properties['prefix']));
        }
        if (array_key_exists('suffix', $properties)) {
            $this->withSuffix(Type::mixedToString($properties['suffix']));
        }
        return $this;
    }

    /**
     * @param string $prefix
     *
     * @return $this
     */
    public function withPrefix(string $prefix): this
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @param string $suffix
     *
     * @return $this
     */
    public function withSuffix(string $suffix): this
    {
        $this->suffix = $suffix;
        return $this;
    }

    public function get(\DOMNode $node): mixed
    {
        $elements = $this->findAll($node, $this->selector);
        if ($elements !== null && $elements->length > 0 && $elements->item(0) !== null) {
            $element = $elements->item(0);
            if ($this->attribute !== null) {
                $result = $element->getAttribute($this->attribute);
            } else {
                $result = $element->textContent;
            }

            if (!Type::isTextEmpty($this->prefix)) {
                $result = $this->prefix . $result;
            }
            if (!Type::isTextEmpty($this->suffix)) {
                $result = $result . $this->suffix;
            }
            return $result;
        }
        return null;
    }
}
