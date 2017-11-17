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

class ConstantGetter extends AbstractGetter
{
    /**
     * @var string
     */
    protected ?string $value;

    public function createFrom(array<string, string> $properties): this
    {
        return $this->withValue($properties['value']);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function withValue(string $value): this
    {
        $this->value = $value;
        return $this;
    }

    public function get(\DOMNode $node): mixed
    {
        return $this->value;
    }
}
