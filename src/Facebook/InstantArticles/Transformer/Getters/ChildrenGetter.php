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
use Facebook\InstantArticles\Transformer\Transformer;

class ChildrenGetter extends ElementGetter
{
    public function get(\DOMNode $node): ?\DOMNode
    {
        $element = parent::get($node);
        if ($element && $element instanceof \DOMNode) {
            $fragment = $element->ownerDocument->createDocumentFragment();
            foreach ($element->childNodes as $child) {
                Transformer::markAsProcessed($child);
                $fragment->appendChild(Transformer::cloneNode($child));
            }
            if ($fragment->hasChildNodes()) {
                return $fragment;
            }
        }
        return null;
    }
}
