<?hh
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Getters;

use Symfony\Component\CssSelector\CssSelectorConverter;

/**
 * Class abstract for all getters.
 */
abstract class AbstractGetter
{
    /**
     * @var string
     */
    protected ?string $selector;

    /**
     * @var string
     */
    protected ?string $attribute;

    /**
     * @param string $selector
     *
     * @return $this
     */
    public function withSelector(string $selector): AbstractGetter
    {
        $this->selector = $selector;
        return $this;
    }

    /**
     * @param string $attribute
     *
     * @return $this
     */
    public function withAttribute(string $attribute): AbstractGetter
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * @param \DOMNode $node
     * @param string $selector
     * @return \DOMNodeList
     */
    public function findAll(\DOMNode $node, ?string $selector): \DOMNodeList
    {
        $domXPath = new \DOMXPath($node->ownerDocument);
        $converter = new CssSelectorConverter();
        $xpath = $converter->toXPath($selector);
        return $domXPath->query($xpath, $node);
    }

    /**
     * Method that should be implemented so it can be Instantiated by GetterFactory
     *
     * @param string[] $configuration With all properties of this Getter
     * @see GetterFactory.
     *
     * @return static
     */
    abstract public function createFrom(dict<string, mixed> $configuration): AbstractGetter;

    /**
     * Method that should retrieve
     *
     * @param \DOMNode $node
     *
     * @return mixed (depending on the Getter implementing class). @see get<type>
     */
    abstract public function get(\DOMNode $node): mixed;

    /**
     * Auxiliary method to extract full qualified class name.
     *
     * @return string The full qualified name of class
     */
    public static function getClassName(): string
    {
        return get_called_class();
    }

    public function getSelector(): ?string
    {
        return $this->selector;
    }

    public function getAttribute(): ?string
    {
        return $this->attribute;
    }
}
