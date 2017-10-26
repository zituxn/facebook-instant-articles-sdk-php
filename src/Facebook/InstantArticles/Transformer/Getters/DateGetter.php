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

class DateGetter extends AbstractGetter
{
    /**
     * @var string
     */
    protected ?string $format;

    public function createFrom(Map<string, string> $properties): DateGetter
    {
        if (isset($properties['selector'])) {
            $this->withSelector($properties['selector']);
        }
        if (isset($properties['attribute'])) {
            $this->withAttribute($properties['attribute']);
        }
        if (isset($properties['format'])) {
            $this->withFormat($properties['format']);
        }
        return $this;
    }

    /**
     * @param string $format
     *
     * @return $this
     */
    public function withFormat(string $format): DateGetter
    {
        $this->format = $format;
        return $this;
    }

    public function get(\DOMNode $node): ?\DateTime
    {
        $elements = $this->findAll($node, $this->selector);
        if (!empty($elements) && $elements->item(0)) {
            $element = $elements->item(0);

            if ($this->format) {
                if ($this->attribute) {
                    return \DateTime::createFromFormat($this->format, $element->getAttribute($this->attribute));
                }
                return \DateTime::createFromFormat($this->format, $element->textContent);
            }
        }
        return null;
    }
}
