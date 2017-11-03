<?hh
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Element;
use Facebook\InstantArticles\Transformer\Getters\AbstractGetter;
use Symfony\Component\CssSelector\CssSelectorConverter;
use Facebook\InstantArticles\Transformer\Getters\GetterFactory;
use Facebook\InstantArticles\Validators\Type;

abstract class ConfigurationSelectorRule extends Rule
{
    /**
     * @var string
     */
    protected ?string $selector;

    /**
     * @var AbstractGetter[]
     */
    protected Map<string, AbstractGetter> $properties = Map {};

    /**
     * @param string $selector
     *
     * @return $this
     */
    public function withSelector(string $selector): ConfigurationSelectorRule
    {
        $this->selector = $selector;

        return $this;
    }

    /**
     * @param $property
     * @param array $value
     *
     * @return $this
     */
    public function withProperty(string $property, array $value): ConfigurationSelectorRule
    {
        if ($value) {
            $this->properties[$property] = GetterFactory::create($value);
        }
        return $this;
    }

    public function withProperties(Vector<string> $properties, Map<string, mixed> $configuration): void
    {
        foreach ($properties as $property) {
            $this->withProperty(
                $property,
                Type::mixedToArray(self::retrieveProperty($configuration, $property))
            );
        }
    }

    public function matchesContext(Element $context): bool
    {
        if ($context instanceof $this->getContextClass()) {
            return true;
        }
        return false;
    }

    public function matchesNode(\DOMNode $node): bool
    {
        // Only matches DOMElements (ignore text and comments)
        if (!$node instanceof \DOMNode) {
            return false;
        }

        // Handles selector = tag
        if ($node->nodeName === $this->selector) {
            return true;
        }

        // Handles selector = .class
        if (preg_match('/^\.[a-zA-Z][a-zA-Z0-9-]*$/', $this->selector) === 1) {
            // Tries every class
            $classNames = explode(' ', Type::nodeAsElement($node)->getAttribute('class'));
            foreach ($classNames as $className) {
                if ('.' . $className === $this->selector) {
                    return true;
                }
            }

            // No match!
            return false;
        }

        // Handles selector = tag.class
        if (preg_match('/^[a-zA-Z][a-zA-Z0-9-]*(\.[a-zA-Z][a-zA-Z0-9-]*)?$/', $this->selector) === 1) {
            // Tries every class
            $classNames = explode(' ', Type::nodeAsElement($node)->getAttribute('class'));
            foreach ($classNames as $className) {
                if ($node->nodeName . '.' . $className === $this->selector) {
                    return true;
                }
            }

            // No match!
            return false;
        }

        // Proceed with the more expensive XPath query
        $document = $node->ownerDocument;
        $domXPath = new \DOMXPath($document);

        if (substr($this->selector, 0, 1) === '/') {
            $xpath = $this->selector;
        } else {
            $converter = new CssSelectorConverter();
            $xpath = $converter->toXPath($this->selector);
        }

        $results = $domXPath->query($xpath);

        if (false !== $results) {
            foreach ($results as $result) {
                if ($result === $node) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param \DOMNode $node
     * @param string $selector
     *
     * @return \DOMNodeList
     */
    public function findAll(\DOMNode $node, string $selector): \DOMNodeList
    {
        $domXPath = new \DOMXPath($node->ownerDocument);
        $converter = new CssSelectorConverter();
        $xpath = $converter->toXPath($selector);
        return $domXPath->query($xpath, $node);
    }

    /**
     * @param $property_name
     * @param $node
     * @return null
     */
    public function getProperty(string $property_name, \DOMNode $node): mixed
    {
        $value = null;
        if (isset($this->properties[$property_name])) {
            $value = $this->properties[$property_name]->get($node);
        }
        return $value;
    }

    /**
     * @param $property_name
     * @param $node
     * @return the string
     */
    public function getPropertyString(string $property_name, \DOMNode $node): string
    {
        $value = $this->getProperty($property_name, $node);
        invariant(is_string($value), 'Error, $value not string');
        return $value;
    }

    public function getProperties(): Map<string, AbstractGetter>
    {
        return $this->properties;
    }
}
