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

class DateGetter extends AbstractGetter
{
    /**
     * @var string
     */
    protected ?string $format;

    public function createFrom(array<string, string> $properties): this
    {
        if (array_key_exists('selector', $properties)) {
            $this->withSelector($properties['selector']);
        }
        if (array_key_exists('attribute', $properties)) {
            $this->withAttribute($properties['attribute']);
        }
        if (array_key_exists('format', $properties)) {
            $this->withFormat($properties['format']);
        }
        return $this;
    }

    /**
     * @param string $format
     *
     * @return $this
     */
    public function withFormat(string $format): this
    {
        $this->format = $format;
        return $this;
    }

    public function get(\DOMNode $node): mixed
    {
        $elements = $this->findAll($node, $this->selector);
        if ($elements !== null && $elements->length > 0 && $elements->item(0)) {
            $element = $elements->item(0);

            if ($this->format !== null) {
                if ($this->attribute !== null) {
                    return \DateTime::createFromFormat($this->format, $element->getAttribute($this->attribute));
                }
                return \DateTime::createFromFormat($this->format, $element->textContent);
            }
        }
        return null;
    }
}
