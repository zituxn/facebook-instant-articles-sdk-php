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

class UnescapedGetter extends ChildrenGetter
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
        $elements = self::findAll($node, $this->selector);
        if (!empty($elements) && $elements->item(0)) {
            $element = $elements->item(0);
            $content = '';
            foreach ($element->childNodes as $child) {
                $content = $content.$child->ownerDocument->saveHTML($child);
            }
            if ($content !== '') {
                return $content;
            }
        }
        return null;
    }
}
