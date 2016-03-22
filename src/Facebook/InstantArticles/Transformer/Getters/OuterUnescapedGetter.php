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
use Symfony\Component\CssSelector\CssSelectorConverter;

class OuterUnescapedGetter extends ChildrenGetter
{
    public function createFrom($properties)
    {
        if (isset($properties['selector'])) {
            $this->withSelector($properties['selector']);
        }
    }

    public function get($node)
    {
        Type::enforce($node, \DOMNode::class);
        $content = '';
        $elements = self::findAll($node, $this->selector);
        foreach ($elements as $element) {
            $content = $content.$element->ownerDocument->saveHTML($element);
        }
        if ($content !== '') {
            return $content;
        }
        return null;
    }
}
