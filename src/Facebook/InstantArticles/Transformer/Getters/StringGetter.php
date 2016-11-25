<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Getters;

use Facebook\InstantArticles\Validators\Type;

class StringGetter extends ChildrenGetter
{
    /**
     * @var string
     */
    protected $attribute;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var string
     */
    protected $sufix;

    public function createFrom($properties)
    {
        if (isset($properties['selector'])) {
            $this->withSelector($properties['selector']);
        }
        if (isset($properties['attribute'])) {
            $this->withAttribute($properties['attribute']);
        }
        if (isset($properties['prefix'])) {
            $this->withPrefix($properties['prefix']);
        }
        if (isset($properties['sufix'])) {
            $this->withSufix($properties['sufix']);
        }
    }

    /**
     * @param string $attribute
     *
     * @return $this
     */
    public function withAttribute($attribute)
    {
        Type::enforce($attribute, Type::STRING);
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * @param string $prefix
     *
     * @return $this
     */

    public function withPrefix($prefix)
    {
        Type::enforce($prefix, Type::STRING);
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @param string $sufix
     *
     * @return $this
     */
    public function withSufix($sufix)
    {
        Type::enforce($sufix, Type::STRING);
        $this->sufix = $sufix;

        return $this;
    }

    public function get($node)
    {
        Type::enforce($node, 'DOMNode');
        $elements = self::findAll($node, $this->selector);
        if (!empty($elements) && $elements->item(0)) {
            $element = $elements->item(0);
            if ($this->attribute) {
                $result = $element->getAttribute($this->attribute);
            } else {
              $result = $element->textContent;
            }

            if (!Type::isTextEmpty($this->prefix)) {
                $result = $this->prefix . $result;
            }
            if (!Type::isTextEmpty($this->sufix)) {
                $result = $result . $this->sufix;
            }
            return $result;
        }
        return null;
    }
}
