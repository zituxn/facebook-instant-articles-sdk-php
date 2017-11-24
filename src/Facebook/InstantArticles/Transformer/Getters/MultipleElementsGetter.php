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

class MultipleElementsGetter extends AbstractGetter
{
    /**
     * @var Getters
     */
    protected array<AbstractGetter> $children = array();

    public function createFrom(array<string, mixed> $properties): this
    {
        $v = $properties['children'];
        invariant(is_array($v), "Not array");
        foreach ($v as $childName => $getter_configuration) {
            $this->children[] = GetterFactory::create($getter_configuration);
        }

        return $this;
    }

    public function get(\DOMNode $node): mixed
    {
        $fragment = $node->ownerDocument->createDocumentFragment();
        foreach ($this->children as $child) {
            $cloned_node = $child->get($node);
            if ($cloned_node instanceof \DOMNode) {
                $fragment->appendChild($cloned_node);
            }
        }
        if ($fragment->hasChildNodes()) {
            return $fragment;
        }
        return null;
    }
}
