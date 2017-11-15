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
use Facebook\InstantArticles\Transformer\Transformer;
use Symfony\Component\CssSelector\CssSelectorConverter;

class ElementGetter extends AbstractGetter
{
    public function createFrom(array<string, string> $properties): ElementGetter
    {
        $this->withSelector($properties['selector']);
        return $this;
    }

    public function get(\DOMNode $node): mixed
    {
        $elements = $this->findAll($node, $this->selector);
        if (!empty($elements) && isset($elements->length) && $elements->length > 0) {
            Transformer::markAsProcessed($elements->item(0));
            return Transformer::cloneNode($elements->item(0));
        }
        return null;
    }
}
