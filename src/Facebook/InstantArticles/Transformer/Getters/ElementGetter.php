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
use Symfony\Component\CssSelector\CssSelectorConverter;

class ElementGetter extends AbstractGetter
{
    public function createFrom(dict<string, mixed> $properties): this
    {
        $this->withSelector(Type::mixedToString($properties['selector']));
        return $this;
    }

    public function get(\DOMNode $node): mixed
    {
        $elements = $this->findAll($node, $this->selector);
        if ($elements !== null && $elements->length > 0) {
            Transformer::markAsProcessed($elements->item(0));
            return $elements->item(0);
        }
        return null;
    }
}
